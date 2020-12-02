<?php


namespace App\Helpers;


use App\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image;
use \LasseRafn\InitialAvatarGenerator\InitialAvatar;

class Utils
{
    public static function generateUsername($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            abort(500, "Internal Server Error: Invalid Email");
        }

        $usernameOfEmail = explode("@", $email)[0];

        preg_match('/^(.*[^0-9])([0-9]*)$/', $usernameOfEmail, $matches1);

        $namePart = $matches1[1];

        $usernames = User::where('username', 'REGEXP', $namePart . '[0-9]*')
            ->latest()
            ->pluck('username')
            ->toArray();

        rsort($usernames);

        if (!count($usernames)) {
            $generatedUsername = $usernameOfEmail;
        } else {

            $perfMatch = $usernames[0];

            preg_match('/^(.*[^0-9])([0-9]*)$/', $perfMatch, $matches2);

            if (!$matches2[2]) {
                $generatedUsername = $namePart . '1';

            } else {
                $numberPart = $matches2[2];

                preg_match('/^([0]*)?([1-9][0-9]*)/', $numberPart, $matches3);

                $zeros = $matches3[1];
                $numbers = $matches3[2] + 1;
                $numberPart = $zeros . $numbers;

                $generatedUsername = $namePart . $numberPart;
            }

        }

        return $generatedUsername;
    }

    public static function generateAvatar(User $user, $size = 128)
    {
        return (new InitialAvatar())
            ->name("$user->first_name $user->last_name")
            ->background('#009688')
            ->color('#ffffff')
            ->size(is_numeric($size) ? $size : 128)
            ->generate();
    }

    public static function resizeImage($path, $save = true)
    {
        $image = Image::make($path);
        $image->orientate();
        $imageFileSize = $image->filesize() / 1024;

        if ($imageFileSize >= 512) {
            $resizePercent = (768 >= $imageFileSize && $imageFileSize >= 512) ? 0.35 : 0.20;
            $newWidth = $image->width() * $resizePercent;

            $image = $image->resize($newWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });

            if ($save) $image->save();
        }

        return $image;
    }

    public static function formatPagination(LengthAwarePaginator $paginator)
    {
        $meta = $paginator->toArray();
        $data = $meta['data'] ?? [];

        if (isset($meta['data'])) unset($meta['data']);

        return [
            'meta' => $meta,
            'data' => $data
        ];
    }

    public static function extractNonNull(array $data)
    {
        return array_filter($data, function ($v) {
            return !is_array($v) && !is_null($v) && !empty(trim($v));
        });
    }

    public static function paginate($collection, int $perPage = 20)
    {
        $itemsPerPage = $perPage > 0 ? $perPage : 20;

        if (request()->has('per_page') && is_numeric(request()->input('per_page'))) {
            $itemsPerPage = request()->input('per_page');
        }

        return self::formatPagination($collection->paginate($itemsPerPage));
    }

    /***
     * @credit: https://stackoverflow.com/questions/35091574/escaping-wildcards-in-like-sql-query-with-eloquent
     * @param string $value
     * @param string $char
     * @return string|string[]
     */
    public static function escapeLike(string $value, string $char = '\\')
    {
        return str_replace(
            [$char, '%', '_'],
            [$char . $char, $char . '%', $char . '_'],
            $value
        );
    }

    public static function cleanSearchValue($value)
    {
        $value = trim(str_replace('%', '', $value));
        $value = !empty($value) ? "%$value%" : '';

        return $value;
    }

    public static function createSearchValues(string $str) : array
    {
        $str = preg_replace('/\s+/', ' ', $str);
        $words = explode(' ', $str);

        $searchValues = [];

        foreach ($words as $i => $w) {
            $slice = array_slice($words, $i);

            $length = 0;
            while ($pos < count($slice)) {
                $pos++;
                $searchValues[] = implode(' ', array_slice($slice, 0, $length));
            }
        }

        return $searchValues;
    }

    public static function getLikeableClass($typeName)
    {
        return config('database.likeable_types')[$typeName] ?? null;
    }

    public static function isLikeable($class)
    {
        $clazz = is_object($class) ? get_class($class) : $class;

        return in_array($clazz, config('database.likeable_types')) &&
            is_subclass_of($clazz, Model::class);
    }
}
