<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @OA\Schema(
 *     description="Customer model",
 *     title="Customer",
 *     @OA\Property(property="id", type="integer", description="Customer ID"),
 *     @OA\Property(property="first_name", type="string", description="First name of the customer"),
 *     @OA\Property(property="last_name", type="string", description="Last name of the customer"),
 *     @OA\Property(property="dob", type="string", format="date", description="Date of birth of the customer"),
 *     @OA\Property(property="username", type="string", description="Username of the customer"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Date and time when the customer was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Date and time when the customer was last updated"),
 * )
 */
class Customer extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @var string[]
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'dob',
        'username',
        'password',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'password' => 'hashed',
    ];
}
