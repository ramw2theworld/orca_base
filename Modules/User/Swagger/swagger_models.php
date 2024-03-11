<?php

/**
 * @OA\Schema(
 *   schema="User",
 *   type="object",
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
 *     property="username",
 *     type="string",
 *     description="Username of the User"
 *   ),
 *   @OA\Property(
 *     property="status",
 *     type="string",
 *     description="Status of the User"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     description="Date of creation of the User"
 *   )
 */

 /**
 * @OA\Schema(
 *   schema="Role",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     format="int64",
 *     description="Unique identifier for the Role"
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="Name of the Role"
 *   ),
 *   @OA\Property(
 *     property="slug",
 *     type="string",
 *     description="slug of the Role"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     description="Date of creation of the Role"
 *   ),
 *   @OA\Property(
 *     property="role",
 *     ref="#/components/schemas/Role"
 *   )
 * )
 */


  /**
 * @OA\Schema(
 *   schema="Permission",
 *   type="object",
 *   @OA\Property(
 *     property="id",
 *     type="integer",
 *     format="int64",
 *     description="Unique identifier for the Permission"
 *   ),
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     description="Name of the Permission"
 *   ),
 *   @OA\Property(
 *     property="slug",
 *     type="string",
 *     description="Slug of the Permission"
 *   ),
 *   @OA\Property(
 *     property="status",
 *     type="string",
 *     format="bool",
 *     description="Status of the Permission"
 *   ),
 *   @OA\Property(
 *     property="created_at",
 *     type="string",
 *     format="date-time",
 *     description="Date of creation of the Permission"
 *   ),
 *   @OA\Property(
 *     property="role",
 *     ref="#/components/schemas/Permission"
 *   )
 * )
 */