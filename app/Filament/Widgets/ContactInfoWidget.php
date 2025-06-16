<?php

namespace App\Filament\Widgets;

use App\Models\Contact;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class ContactInfoWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.contact-info-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 0;

    public function getViewData(): array
    {
        $contact = Contact::first();

        return [
            'contact' => $contact,
            'hasContact' => (bool) $contact,
        ];
    }

    public function editContactAction(): Action
    {
        return Action::make('editContact')
            ->label('تعديل جهة الاتصال')
            ->icon('heroicon-m-pencil-square')
            ->color('primary')
            ->form([
                Forms\Components\Section::make('معلومات الاتصال')
                    ->description('إدارة تفاصيل جهة الاتصال الخاصة بك')
                    ->schema([
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('رقم واتساب')
                            ->tel()
                            ->placeholder('+1234567890')
                            ->helperText('قم بتضمين رمز الدولة (مثال: +1234567890)')
                            ->maxLength(20)
                            ->prefixIcon('heroicon-m-chat-bubble-left-ellipsis'),
                        Forms\Components\TextInput::make('phone')
                            ->label('رقم الهاتف')
                            ->tel()
                            ->placeholder('+1234567890')
                            ->helperText('قم بتضمين رمز الدولة (مثال: +1234567890)')
                            ->maxLength(20)
                            ->prefixIcon('heroicon-m-phone'),
                        Forms\Components\TextInput::make('facebook')
                            ->label('رابط الفيسبوك')
                            ->url()
                            ->placeholder('https://facebook.com/yourpage')
                            ->helperText('أدخل رابط صفحة الفيسبوك الخاصة بك')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-globe-alt'),

                        Forms\Components\TextInput::make('instagram')
                            ->label('رابط الإنستجرام')
                            ->url()
                            ->placeholder('https://instagram.com/yourpage')
                            ->helperText('أدخل رابط صفحة الإنستجرام الخاصة بك')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-camera'),
                    ])
                    ->columns(2)
            ])
            ->fillForm(function (): array {
                $contact = Contact::first();
                return $contact ? $contact->toArray() : [];
            })
            ->action(function (array $data): void {
                $contact = Contact::first();
                if ($contact) {
                    $contact->update($data);
                    $message = 'تم تحديث معلومات الاتصال بنجاح!';
                } else {
                    Contact::create($data);
                    $message = 'تم إنشاء معلومات الاتصال بنجاح!';
                }
                Notification::make()
                    ->title($message)
                    ->success()
                    ->send();
            })
            ->modalHeading('إعدادات الاتصال')
            ->modalSubmitActionLabel('حفظ التغييرات')
            ->modalWidth('lg');
    }

    public static function canView(): bool
    {
        return true;
    }
}