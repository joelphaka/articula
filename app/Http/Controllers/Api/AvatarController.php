<?php


namespace App\Http\Controllers\Api;


use App\Helpers\Utils;
use App\Http\Controllers\Controller;
use App\Http\Requests\Avatar\UploadAvatarRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function index($filename, Request $request)
    {
        $hashId = pathinfo($filename)['filename'];
        $format = pathinfo($filename)['extension'] ?? 'png';
        $types = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
        ];

        if (in_array($format, array_keys($types)) && ($user = User::findByHashid($hashId))) {

            $type = $types[$format];
            $imageFilename = "{$hashId}.png";

            if (Storage::disk('avatars')->exists($imageFilename)) {

                $image = Storage::disk('avatars')->get($imageFilename);

                return response($image, 200, [
                    'Content-Type' => $type
                ]);
            }

            return Utils::generateAvatar($user)->response($format, 100);
        }

        return response('Image Not Found', 404);
    }

    public function store(UploadAvatarRequest $request)
    {
        $filename = auth()->user()->hashid() . '.png';
        $request->file('avatar')->storeAs('/', $filename, 'avatars');
        $path = Storage::disk('avatars')->getAdapter()->getPathPrefix() . $filename;

        Utils::resizeImage($path);

        auth()->user()->update(['has_avatar' => true]);

        return response()->json([
            'message' => 'The avatar was uploaded',
            'user' => auth()->user()
        ], 200);
    }

    public function destroy()
    {
        $filename = auth()->user()->hashid.'.png';
        if ($filename) {
            if (Storage::disk('avatars')->exists($filename)) {
                Storage::disk('avatars')->delete($filename);
                auth()->user()->update(['has_avatar' => false]);

                return response()->json([
                    'message' => 'The avatar was removed.',
                    'is_removed' => true,
                    'user' => auth()->user()
                ], 200);
            }
        }

        return response()->json([
            'message' => 'The avatar was not removed.',
            'is_removed' => false,
            'user' => auth()->user()
        ], 200);
    }
}
