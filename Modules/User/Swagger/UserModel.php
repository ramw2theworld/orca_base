<?php
// path = Modules\User\Swagger\UserModel.php
/**
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   required={"id", "first_name", "last_name", "email", "role"},
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     format="int64",
 *     description="Unique identifier for the User"
 *   ),
 *   @OA\Property(
 *     property="first_name",
 *     type="string",
 *     description="First name of the User"
 *   ),
 *   @OA\Property(
 *     property="last_name",
 *     type="string",
 *     description="Last name of the User"
 *   ),
 *   @OA\Property(
 *     property="email",
 *     type="string",
 *     format="email",
 *     description="Email address of the User"
 *   ),
 *   @OA\Property(
 *     property="role",
 *     type="object",
 *     ref="#/components/schemas/Role"
 *   )
 * )
 */
