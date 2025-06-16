<?php

namespace App\Http\Controllers;

use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/contact",
     *     summary="Get the first contact",
     *     description="Returns the first contact from the database",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="id",
     *                 type="integer",
     *                 example=1
     *             ),
     *             @OA\Property(
     *                 property="whatsapp",
     *                 type="string",
     *                 example="https://whatsapp.com/phone=+9631123131231"
     *             ),
     *             @OA\Property(
     *                 property="phone",
     *                 type="string",
     *                 example="j+96311231231"
     *             )
     *         )
     *     )
     * )
     */
    public function __invoke()
    {
        return response()->json(
            Contact::query()->first()
        );
    }
}
