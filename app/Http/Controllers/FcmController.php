<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\FcmToken;
use App\Models\FcmRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\NewProductNotification;

/**
 * @OA\Post(
 *     path="/api/fcm/update",
 *     summary="Register or update an FCM token",
 *     tags={"FCM"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"token"},
 *             @OA\Property(property="token", type="string", example="fcm_token_example")
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="No content"
 *     )
 * )
 */
class FcmController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

       FcmToken::query()->updateOrCreate([
         'token' => $request->input('token')
       ]);

        return response()->noContent();
    }

    /**
     * @OA\Post(
     *     path="/api/fcm/destroy",
     *     summary="Delete an FCM token",
     *     tags={"FCM"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="fcm_token_example")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="No content"
     *     )
     * )
     */
    public function destroy(Request $request)
    {
       FcmToken::query()->where('token' , $request->input('token'))->delete();

       return response()->noContent();
    }


    /**
     * @OA\Post(
     *     path="/api/fcm/test",
     *     summary="Send a test notification to a specific FCM token",
     *     tags={"FCM"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token"},
     *             @OA\Property(property="token", type="string", example="fcm_token_example"),
     *             @OA\Property(property="productId", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Notification sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Test notification sent to single token"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="fcmToken", type="string"),
     *                 @OA\Property(property="product", type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="image_url", type="string", nullable=true)
     *                 ),
     *                 @OA\Property(property="notificationData", type="object",
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="body", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No products found in database"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to send notification"
     *     )
     * )
     */
    public function testToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'productId' => 'nullable|exists:products,id'
        ]);

        try {
            // Get product (random if not specified)
            if ($request->has('productId')) {
                $product = Product::findOrFail($request->input('productId'));
            } else {
                $product = Product::inRandomOrder()->first();

                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No products found in database'
                    ], 404);
                }
            }

            // Create recipient for the specific token
            $recipient = new FcmRecipient($request->input('token'));

            // Send notification
            $recipient->notify(new NewProductNotification($product));

            return response()->json([
                'success' => true,
                'message' => 'Test notification sent to single token',
                'data' => [
                    'fcmToken' => $request->input('token'),
                    'product' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'image_url' => $product->getFirstMediaUrl('images') ?? null,
                    ],
                    'notificationData' => [
                        'title' => 'New Product Available!',
                        'body' => "Check out our new product: {$product->name}",
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to send single token notification: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification to single token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
