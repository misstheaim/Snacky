<?php

namespace App\Filament\Resources\Templates;

use App\Contracts\Filament\Snack\FormTemplate;
use App\Filament\Resources\Helpers\HelperFunctions;
use App\Models\Snack;
use App\Services\Helpers\HelperSortProductData;
use App\Services\UzumHttpProductReceiver;
use Closure;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\SelectColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SnackFormTemplate implements FormTemplate
{
    public $isAdmin;
    public $isManager;

    public function __construct()
    {
        $this->isAdmin = HelperFunctions::isUser(Auth::user())->isAdmin();
        $this->isManager = HelperFunctions::isUser(Auth::user())->isManager();
    }

    public function __invoke()
    {
        $from = array(
            TextInput::make('link')
                ->required()
                ->activeUrl()
                ->rules(['valid_product' => fn() => function (string $attribute, mixed $value, Closure $fail) {
                    if (parse_url($value)['host'] != config('uzum.hostname')) {
                        $fail('Incorrect hostname');
                        return;
                    }

                    $reciever = new UzumHttpProductReceiver();
                    $id = Str::of($value)->match('/(?<=-)\d+(?!.*(?<=-)\d+)/');

                    $record = $reciever->receiveProductData($id);

                    if (isset($record['failed'])) {
                        $fail('This is the wrong link');
                        return;
                    }
                    $data = HelperSortProductData::getSortedProduct($record);

                    if (is_null($data['category_id'])) {
                        $fail('The link must be from Product category');
                    }
                    if (Snack::where('uzum_product_id', $data['uzum_product_id'])->exists()) {
                        $fail('The product already exists');
                    }

                    HelperFunctions::$buffer = $data;
                }])
                ->label('Add a link to the product on Uzum Market')
                ->validationMessages([
                    'required' => 'Please add a link to the product.',
                    'active_url' => 'This field must be a valid URL.'
                ]),
            Hidden::make('user_id')
                ->default(fn () => Auth::user()->id),
            Hidden::make('title_ru'),
            Hidden::make('title_uz'),
            Hidden::make('uzum_product_id'),
            Hidden::make('price'),
            Hidden::make('category_id'),
            Hidden::make('description_ru'),
            Hidden::make('high_image_link'),
            Hidden::make('low_image_link'),
        );

        return $from;
    }
}