<?php

/**
 * @OA\Info(
 *   title="Your API Title",
 *   version="1.0.0",
 *   description="A description of your API",
 *   @OA\Contact(
 *     email="support@example.com"
 *   )
 * )
 */

/**
 * @OA\Get(
 *   path="/api/users",
 *   summary="List Users",
 *   @OA\Response(
 *     response=200,
 *     description="A list of users",
 *     @OA\JsonContent(
 *       type="array",
 *       @OA\Items(ref="#/components/schemas/User")
 *     )
 *   ),
 *   tags={"Users"}
 * )
 */

/**
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="John Doe"),
 *   // Additional properties...
 * )
 */
