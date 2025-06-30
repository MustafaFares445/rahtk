<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\School;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\ProductTypes;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\SchoolResource\Pages;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;
    protected static ?string $navigationLabel = 'المدارس';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                self::schoolInformationSection(),
                self::schoolAttributesSection(),
                self::managementProfileSection(),

                Forms\Components\Tabs::make('تفاصيل إضافية')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('الخدمات والأنشطة')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([self::servicesActivitiesSection()]),

                        Forms\Components\Tabs\Tab::make('الهيئة التدريسية')
                            ->icon('heroicon-o-users')
                            ->schema([self::teachingStaffSection()]),

                        Forms\Components\Tabs\Tab::make('إدارة الفصول')
                            ->icon('heroicon-o-book-open')
                            ->schema([self::classManagementSection()]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::tableColumns())
            ->filters([

                ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn ($query) => $query->with(['product', 'media']));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }

    protected static function schoolInformationSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('معلومات المدرسة')
            ->icon('heroicon-o-information-circle')
            ->description('المعلومات الأساسية عن المدرسة')
            ->collapsible()
            ->schema([
                Forms\Components\TextInput::make('product.title')
                    ->required()
                    ->maxLength(255)
                    ->label('اسم المدرسة')
                    ->helperText('أدخل الاسم الرسمي للمدرسة.')
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\Textarea::make('product.description')
                    ->required()
                    ->columnSpanFull()
                    ->label('وصف المدرسة')
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\TextInput::make('product.address')
                    ->required()
                    ->maxLength(255)
                    ->label('عنوان المدرسة')
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\SpatieMediaLibraryFileUpload::make('product.image')
                    ->collection('primary-image')
                    ->image()
                    ->preserveFilenames()
                    ->required()
                    ->label('صور المدرسة')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp' , 'image/jpg'])
                    ->helperText('قم بتحميل صورة واحدة أو أكثر للمدرسة (JPEG, PNG, GIF, WebP).')
                    ->reorderable()
                    ->imagePreviewHeight('100')
                    ->extraInputAttributes(['dir' => 'rtl']),
            ]);
    }

    protected static function schoolAttributesSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('خصائص المدرسة')
            ->icon('heroicon-o-bookmark')
            ->description('المعلومات الرئيسية والتاريخية')
            ->collapsible()
            ->columns(2)
            ->schema([
                Forms\Components\TextInput::make('working_duration')
                    ->required()
                    ->maxLength(255)
                    ->label('ساعات العمل')
                    ->placeholder('8:00 ص - 4:00 م')
                    ->hint('ساعات الدوام اليومي')
                    ->prefixIcon('heroicon-o-clock')
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\Select::make('founding_date')
                    ->required()
                    ->label('تاريخ التأسيس')
                    ->options(function () {
                        $currentYear = now()->year;
                        $startYear = 1600;
                        $years = [];

                        for ($year = $currentYear; $year >= $startYear; $year--) {
                            $years[$year] = $year;
                        }

                        return $years;
                    })
                    ->searchable()
                    ->placeholder('اختر السنة')
                    ->hint('متى تأسست المدرسة؟')
                    ->prefixIcon('heroicon-o-cake')
                    ->extraInputAttributes(['dir' => 'rtl'])
            ]);
    }

    protected static function managementProfileSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('ملف المدير')
            ->icon('heroicon-o-user-circle')
            ->description('معلومات الاتصال والقيادة')
            ->collapsible()
            ->schema([
                Forms\Components\TextInput::make('manager')
                    ->required()
                    ->maxLength(255)
                    ->label('اسم المدير')
                    ->placeholder('علي محمد')
                    ->columnSpan(['sm' => 1])
                    ->hint('الاسم الكامل لمدير المدرسة')
                    ->prefixIcon('heroicon-o-user')
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\SpatieMediaLibraryFileUpload::make('manager-image')
                    ->collection('managers-images')
                    ->image()
                    ->preserveFilenames()
                    ->required()
                    ->label('صورة شخصية')
                    ->columnSpan(['md' => 1])
                    ->reorderable()
                    ->appendFiles()
                    ->imageEditor()
                    ->imageCropAspectRatio('3:4')
                    ->panelLayout('grid')
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp' , 'image/jpg'])
                    ->helperText('قم بتحميل صورة شخصية احترافية (نسبة 3:4 موصى بها)')
                    ->hint('الحد الأقصى 5MB لكل صورة • JPEG أو PNG أو WebP')
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\Textarea::make('manager_description')
                    ->required()
                    ->columnSpanFull()
                    ->label('الملف المهني')
                    ->rows(6)
                    ->placeholder('وصف دور المدير، الخبرة، والمؤهلات...')
                    ->helperText('سيرة مهنية مختصرة (200-300 كلمة موصى بها)')
                    ->extraInputAttributes([
                        'class' => 'prose max-w-full',
                        'dir' => 'rtl',
                        'style' => 'text-align: right;'
                    ]),
            ]);
    }

    protected static function servicesActivitiesSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('الخدمات والأنشطة')
            ->icon('heroicon-o-clipboard-document-list')
            ->description('إدارة الخدمات والأنشطة التي تقدمها المدرسة')
            ->collapsible()
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->image()
                    ->multiple()
                    ->preserveFilenames()
                    ->label('صورة الخدمة')
                    ->columnSpan(2)
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageEditor()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp' , 'image/jpg'])
                    ->helperText('صورة عالية الجودة (نسبة 1:1 موصى بها)')
                    ->downloadable()
                    ->openable()
                    ->extraInputAttributes(['dir' => 'rtl']),
            ]);
    }

    protected static function teachingStaffSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('الهيئة التدريسية')
            ->description('إدارة فريق التدريس بالمدرسة')
            ->icon('heroicon-o-users')
            ->collapsible()
            ->schema([
                Forms\Components\Repeater::make('school_teachers')
                    ->relationship('teachers')
                    ->label('قائمة المدرسين')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->label('الاسم الكامل')
                            ->placeholder('د. سارة جونسون')
                            ->extraInputAttributes(['dir' => 'rtl'])
                            ->live(onBlur: true) // Add live update
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                // Trigger update of teacher options in class management
                                $set('../../teacher_list_updated', time());
                            }),

                        Forms\Components\TextInput::make('job_title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->label('المسمى الوظيفي')
                            ->placeholder('رئيس قسم الرياضيات')
                            ->extraInputAttributes(['dir' => 'rtl']),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                            ->collection('teachers-images')
                            ->image()
                            ->preserveFilenames()
                            ->required()
                            ->label('صورة شخصية')
                            ->columnSpan(2)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageEditor()
                            ->helperText('صورة شخصية عالية الجودة (نسبة 1:1 موصى بها)')
                            ->downloadable()
                            ->openable()
                            ->extraInputAttributes(['dir' => 'rtl']),

                        Forms\Components\Hidden::make('school_id')
                            ->default(function ($operation, $state, Forms\Set $set) {
                                if ($operation === 'create') {
                                    return null;
                                }
                                return $state ?? null;
                            })
                            ->dehydrated(),

                        Forms\Components\Hidden::make('temp_key')
                            ->default(fn () => (string) Str::uuid())
                            ->dehydrated(),
                    ])
                    ->columns(2)
                    ->addActionLabel('+ إضافة مدرس جديد')
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->cloneable()
                    ->defaultItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'مدرس جديد')
                    ->grid(2)
                    ->collapsed()
                    ->helperText('أضف جميع المدرسين ببياناتهم وصورهم')
                    ->live(), // Make the entire repeater live
            ]);
    }

    protected static function classManagementSection(): Forms\Components\Section
    {
        $classes = [
            'kg1' => 'KG1',
            'kg2' => 'KG2',
            'kg3' => 'KG3',
            '1st' => 'الصف الأول',
            '2nd' => 'الصف الثاني',
            '3rd' => 'الصف الثالث',
            '4th' => 'الصف الرابع',
            '5th' => 'الصف الخامس',
            '6th' => 'الصف السادس',
            '7th' => 'الصف السابع',
            '8th' => 'الصف الثامن',
            '9th' => 'الصف التاسع',
            '10th' => 'الصف العاشر',
            '11th' => 'الصف الحادي عشر',
            '12th' => 'بكالوريا',
        ];

        return Forms\Components\Section::make('إدارة الفصول')
            ->description('تنظيم فصول المدرسة مع معلومات مفصلة')
            ->icon('heroicon-o-book-open')
            ->collapsible()
            ->schema([
                Forms\Components\Hidden::make('teacher_list_updated')
                    ->default(0),

                Forms\Components\Repeater::make('school_classes')
                    ->relationship('schoolClasses')
                    ->label('قائمة الفصول')
                    ->schema([
                        Forms\Components\Select::make('name')
                            ->required()
                            ->options($classes)
                            ->columnSpan(1)
                            ->label('اسم الفصل')
                            ->native(false)
                            ->extraInputAttributes(['dir' => 'rtl'])
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state === 'kg1' || $state === 'kg2' || $state === 'kg3') {
                                    $set('type', 'initial');
                                } elseif (in_array($state, ['1st', '2nd', '3rd', '4th', '5th', '6th'])) {
                                    $set('type', 'principal');
                                } elseif (in_array($state, ['7th', '8th', '9th', '10th', '11th', '12th'])) {
                                    $set('type', 'secondary');
                                }
                            })
                            ->live(),

                        Forms\Components\Hidden::make('type')
                            ->required()
                            ->columnSpan(1)
                            ->label('المستوى التعليمي')
                            ->dehydrated(true),

                        Forms\Components\Select::make('teacher')
                            ->label('اختر مدرسين')
                            ->options(function (Forms\Get $get, $operation) {
                                // Get current school teachers from the form
                                $schoolTeachers = collect($get('../../school_teachers') ?? []);

                                // Get the current record (school) if editing
                                $record = $get('../../id');
                                $savedTeachers = collect();

                                if ($record && $operation === 'edit') {
                                    $school = \App\Models\School::find($record);
                                    if ($school) {
                                        $savedTeachers = $school->teachers;
                                    }
                                }

                                $options = [];

                                $tempKeys = [];

                                // Add existing/saved teachers
                                foreach ($savedTeachers as $teacher) {
                                    $options[$teacher->id] = $teacher->name . ' - ' . $teacher->job_title;
                                    $tempKeys[] = $teacher->temp_key;
                                }

                                // Add temporary teachers (from the current form)
                                foreach ($schoolTeachers as $index => $teacher) {
                                    if (!empty($teacher['name']) && !empty($teacher['job_title']) && ! in_array($teacher['temp_key'] , $tempKeys)) {
                                        $tempKey = $teacher['temp_key'] ?? $index;
                                        $options['temp_' . $tempKey] = $teacher['name'] . ' - ' . $teacher['job_title'] . ' (جديد)';
                                    }
                                }

                                return $options;
                            })
                            ->multiple()
                            ->searchable()
                            ->columnSpanFull()
                            ->hint('اختر مدرسين من المدرسين المضافين أعلاه أو من قاعدة البيانات')
                            ->live()
                            ->default(function (Forms\Get $get, $operation) {
                                // Pre-populate with existing teacher assignments when editing
                                if ($operation === 'edit') {
                                    $classId = $get('id');
                                    if ($classId) {
                                        $schoolClass = \App\Models\SchoolClass::find($classId);
                                        if ($schoolClass) {
                                            return $schoolClass->teachers->pluck('id')->toArray();
                                        }
                                    }
                                }
                                return [];
                            }),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('videos')
                            ->collection('videos')
                            ->multiple()
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv'])
                            ->preserveFilenames()
                            ->columnSpanFull()
                            ->label('فيديوهات الفصل')
                            ->downloadable()
                            ->openable()
                            ->helperText('قم بتحميل فيديو تعريفي أو توضيحي للفصل (الحد الأقصى 50MB)')
                            ->hint('صيغة MP4, MOV, AVI, أو WMV')
                            ->extraInputAttributes(['dir' => 'rtl']),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                            ->collection('images')
                            ->image()
                            ->multiple()
                            ->preserveFilenames()
                            ->columnSpanFull()
                            ->label('معرض الصور')
                            ->reorderable()
                            ->appendFiles()
                            ->imageResizeMode('cover')
                            ->imageEditor()
                            ->helperText('قم بتحميل صور للفصل أو أعمال الطلاب أو الأنشطة')
                            ->hint('صيغة JPEG, PNG, GIF, أو WebP')
                            ->directory('class-gallery')
                            ->extraInputAttributes(['dir' => 'rtl']),
                    ])
                    ->columns(2)
                    ->addActionLabel('+ إضافة فصل جديد')
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->cloneable()
                    ->deleteAction(
                        fn ($action) => $action
                            ->before(function ($component, $state) {
                                // Get the school class ID from the current state
                                if (isset($state['id'])) {
                                    $schoolClass = \App\Models\SchoolClass::find($state['id']);
                                    if ($schoolClass) {
                                        // Detach all teachers before deletion
                                        $schoolClass->teachers()->detach();
                                        // Clear media collections
                                        $schoolClass->clearMediaCollection('videos');
                                        $schoolClass->clearMediaCollection('images');
                                    }
                                }
                            })
                            ->requiresConfirmation()
                            ->modalHeading('تأكيد الحذف')
                            ->modalDescription('هل أنت متأكد من حذف هذا الفصل؟ سيتم حذف جميع البيانات المرتبطة به.')
                            ->modalSubmitActionLabel('نعم، احذف')
                            ->modalCancelActionLabel('إلغاء')
                    )
                    ->itemLabel(fn (array $state): ?string => isset($classes[$state['name']]) ? "{$classes[$state['name']]}" : 'فصل جديد')
                    ->grid(2)
                    ->defaultItems(1)
                    ->collapsed(),
            ]);
    }

    protected static function tableColumns(): array
    {
        return [
            ImageColumn::make('images')
                ->label('الصورة')
                ->square()
                ->stacked()
                ->getStateUsing(fn ($record) => $record->getFirstMedia('images')?->getUrl())
                ->size(50),

            Tables\Columns\TextColumn::make('product.title')
                ->label('اسم المدرسة')
                ->sortable()
                ->icon('heroicon-o-academic-cap')
                ->color('primary'),

            Tables\Columns\TextColumn::make('working_duration')
                ->label('ساعات العمل')
                ->searchable()
                ->icon('heroicon-o-clock')
                ->tooltip('ساعات العمل'),

            Tables\Columns\TextColumn::make('founding_date')
                ->label('تاريخ التأسيس')
                ->date()
                ->sortable()
                ->icon('heroicon-o-calendar')
                ->color('success'),

            Tables\Columns\TextColumn::make('manager')
                ->label('المدير')
                ->searchable()
                ->icon('heroicon-o-user')
                ->color('warning'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('تاريخ الإنشاء')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->label('تاريخ التحديث')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}
