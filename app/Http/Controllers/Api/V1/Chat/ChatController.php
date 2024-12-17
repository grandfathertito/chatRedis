<?php

namespace App\Http\Controllers\Api\V1\Chat;

use App\Events\PrivateMessageSent;
use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\GetMessageRequest;
use App\Http\Requests\SendMessageRequest;
use App\Http\Resources\ListUserResource;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
class ChatController extends ApiController
{
    public function get_user_list()
    {
        #dd(decryptMessage('V2dUdlF6dW1hdUx0RDcxNm9KSXBiQT09OjpCyFGaFnPhDTmul9nsfiNT'));
        try {
            $users = User::paginate(self::PAGINATE_COUNT);

            // append data
            $data['data'] = ListUserResource::collection($users);
            $data['pagination']['total'] = $users->total();
            $data['pagination']['per_page'] = $users->perPage();
            $data['pagination']['next_page_url'] = $users->nextPageUrl();
            $data['pagination']['prev_page_url'] = $users->previousPageUrl();
            $data['pagination']['last_page'] = $users->lastPage();
            $data['pagination']['current_page'] = $users->currentPage();

            // send response to front end
            return successResponse( $data, null, 200);
        } catch (\Exception $exception) {
            // send response to front end
            return errorResponse($exception, __('api.Has error'), 500);
        }
    }

    public function send_message(SendMessageRequest $request) {
        $enc_message = encryptMessage($request->content);
        $sender_id = JWTAuth::parseToken()->authenticate()->id;
        $save_message = Message::create([
            'sender_id' => $sender_id,
            'receiver_id' => $request->recived_id,
            'content' => $enc_message,
        ]);

        // push to message real time
        broadcast(new PrivateMessageSent($request->content, $sender_id, $request->recived_id))->toOthers();

        // send response to front end
        return successResponse( MessageResource::make($save_message), __('api.Send Was Success'), 200);
    }

    public function get_message(GetMessageRequest $request) {
        $messages = Message::where([
            ['sender_id', JWTAuth::parseToken()->authenticate()->id],
            ['receiver_id', $request->recived_id],
        ])->orderBy('id', 'desc')->paginate(self::PAGINATE_COUNT);

        // append data
        $data['data'] = MessageResource::collection($messages);
        $data['pagination']['total'] = $messages->total();
        $data['pagination']['per_page'] = $messages->perPage();
        $data['pagination']['next_page_url'] = $messages->nextPageUrl();
        $data['pagination']['prev_page_url'] = $messages->previousPageUrl();
        $data['pagination']['last_page'] = $messages->lastPage();
        $data['pagination']['current_page'] = $messages->currentPage();

        // send response to front end
        return successResponse( $data, null, 200);
    }
}
