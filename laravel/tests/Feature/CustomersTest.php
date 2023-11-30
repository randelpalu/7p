<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class CustomersTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    private string $username = 'test@example.com';
    private string $password = 'password';

    public function setUp(): void
    {
        parent::setUp();

        Customer::factory(5)->create();
        User::create([
            'name' => 'Test User',
            'email' => $this->username,
            'password' => $this->password
        ]);
    }

    private function getBasicAuthHeaders(): array
    {
        return ['Authorization' => 'Basic ' . base64_encode("$this->username:$this->password")];
    }

    public function test_unauthorized_request(): void
    {
        $res = $this->getJson('/api/customers');

        $res->assertStatus(401);
    }

    public function test_get_customers_list(): void
    {
        $res = $this->getJson('/api/customers', $this->getBasicAuthHeaders());
        $resData = $res->json('data');

        $res->assertStatus(200);
        $res->assertJsonStructure([
            'message',
            'data' => [
                '*' => [
                    'id',
                    'created_at',
                    'updated_at',
                    'first_name',
                    'last_name',
                    'dob',
                    'username'
                ],
            ],
        ]);

        foreach ($resData as $item) {
            $this->assertArrayNotHasKey('password', $item);
        }
    }

    public function test_get_customer()
    {
        $this->withoutExceptionHandling();
        $this->assertNotNull(Customer::find(1));

        $res = $this->getJson('/api/customers/1', $this->getBasicAuthHeaders());

        $res->assertStatus(200);
        $res->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'created_at',
                'updated_at',
                'first_name',
                'last_name',
                'dob',
                'username'
            ],
        ]);
        $this->assertArrayNotHasKey('password', $res->json('data'));
    }

    public function test_store_customer(): void
    {
        $customer = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => 'userjohn',
            'password' => 'PasS1234'
        ];

        $res = $this->postJson('/api/customers', $customer, $this->getBasicAuthHeaders());
        $resData = $res->json('data');

        $res->assertStatus(201);
        $this->assertArrayNotHasKey('password', $resData);
        $res->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'created_at',
                'updated_at',
                'first_name',
                'last_name',
                'dob',
                'username'
            ],
        ]);
        $this->assertEquals('userjohn', $resData['username']);
    }

    public function test_store_customer_password_too_short_validation_error(): void
    {
        $customerData = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => 'userjohn',
            'password' => '1'
        ];

        $res = $this->postJson('/api/customers', $customerData, $this->getBasicAuthHeaders());
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['password'])
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'password' => [
                        'The password field must be at least 8 characters.',
                        'The password field format is invalid.'
                    ]
                ]
            ]);
    }

    public function test_store_customer_faulty_password_format_validation_error(): void
    {
        $customerData = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => 'userjohn',
            'password' => '1123123123123'
        ];

        $res = $this->postJson('/api/customers', $customerData, $this->getBasicAuthHeaders());
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['password'])
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'password' => [
                        'The password field format is invalid.'
                    ]
                ]
            ]);
    }

    public function test_store_customer_faulty_username_format_validation_error(): void
    {
        $customerData = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => 'user-jo.hn',
            'password' => 'Abcd12324'
        ];

        $res = $this->postJson('/api/customers', $customerData, $this->getBasicAuthHeaders());
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['username'])
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'username' => [
                        'The username field format is invalid.'
                    ]
                ]
            ]);
    }

    public function test_store_customer_username_already_exists_validation_error(): void
    {
        $existingCustomer = Customer::find(1);
        $customerData = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => $existingCustomer->username,
            'password' => 'Abcd12324'
        ];

        $res = $this->postJson('/api/customers', $customerData, $this->getBasicAuthHeaders());
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['username'])
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'username' => ['The username has already been taken.']
                ]
            ]);
    }

    public function test_update_customer(): void
    {
        $originalCustomer = Customer::find(1);
        $data = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => 'userjohn',
            'password' => 'PasS1234New'
        ];

        $res = $this->putJson('/api/customers/1', $data, $this->getBasicAuthHeaders());

        $res->assertStatus(200);

        $updatedCustomer = Customer::find(1);
        $this->assertNotEquals($originalCustomer['password'], $updatedCustomer['password']);
    }

    public function test_update_customer_with_empty_username(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => '',
            'password' => '12Password'
        ];

        $res = $this->putJson('/api/customers/1', $data, $this->getBasicAuthHeaders());

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['username'])
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'username' => ['The username field is required.']
                ]
            ]);
    }

    public function test_update_customer_with_too_short_username(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => 'a',
            'password' => '12Password'
        ];

        $res = $this->putJson('/api/customers/1', $data, $this->getBasicAuthHeaders());

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['username'])
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'username' => ['The username field must be at least 2 characters.']
                ]
            ]);
    }

    public function test_update_customer_with_invalid_characters_in_username(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => '£½£234ljlk',
            'password' => '12Password'
        ];

        $res = $this->putJson('/api/customers/1', $data, $this->getBasicAuthHeaders());

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['username'])
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'username' => ['The username field format is invalid.']
                ]
            ]);
    }

    public function test_update_customer_with_empty_password(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => 'toomas123',
            'password' => ''
        ];

        $res = $this->putJson('/api/customers/1', $data, $this->getBasicAuthHeaders());

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['password'])
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'password' => ['The password field is required.']
                ]
            ]);
    }

    public function test_update_customer_with_too_short_password(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => 'toomas123',
            'password' => '1Aa'
        ];

        $res = $this->putJson('/api/customers/1', $data, $this->getBasicAuthHeaders());

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['password'])
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'password' => ['The password field must be at least 8 characters.']
                ]
            ]);
    }

    public function test_update_customer_with_faulty_password(): void
    {
        $data = [
            'first_name' => 'John',
            'last_name' => 'Lastname',
            'dob' => '1990-11-11',
            'username' => 'toomas123',
            'password' => '1aaaaaaaaaaaa'
        ];

        $res = $this->putJson('/api/customers/1', $data, $this->getBasicAuthHeaders());

        $res->assertStatus(422)
            ->assertJsonValidationErrors(['password'])
            ->assertJsonFragment([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => [
                    'password' => ['The password field format is invalid.']
                ]
            ]);
    }

    public function test_delete_customer(): void
    {
        $this->assertNotNull(Customer::find(1));

        $res = $this->deleteJson('/api/customers/1', [], $this->getBasicAuthHeaders());

        $res->assertStatus(200);
        $this->assertEquals('Customer 1 deleted successfully', $res['message']);
        $this->assertNull(Customer::find(1));
    }

    public function test_trying_to_delete_nonexisting_customer(): void
    {
        $this->assertNull(Customer::find(1000));

        $res = $this->deleteJson('/api/customers/1000', [], $this->getBasicAuthHeaders());
        $res->assertStatus(404);
    }
}
