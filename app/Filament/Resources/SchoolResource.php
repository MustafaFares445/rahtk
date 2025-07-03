<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\School;
use App\Models\Teacher;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Filament\Resources\SchoolResource\Pages;
use Filament\Forms\Components\Actions\Action as FormAction;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;
    protected static ?string $navigationLabel = 'المدارس';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $recordTitleAttribute = 'product.title';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Group::make([
                            self::schoolInformationSection(),
                            self::schoolAttributesSection(),
                        ])
                        ->columnSpan(2),

                        Forms\Components\Group::make([
                            self::managementProfileSection(),
                            self::quickStatsSection(),
                        ])
                        ->columnSpan(1),
                    ]),

                Forms\Components\Tabs::make('تفاصيل إضافية')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('الخدمات والأنشطة')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->badge(fn ($record) => $record?->getMedia('images')->count() ?: null)
                            ->schema([self::servicesActivitiesSection()]),

                        Forms\Components\Tabs\Tab::make('الهيئة التدريسية')
                            ->icon('heroicon-o-users')
                            ->badge(fn ($record) => $record?->teachers()->count() ?: null)
                            ->schema([self::teachingStaffSection()]),

                        Forms\Components\Tabs\Tab::make('إدارة الفصول')
                            ->icon('heroicon-o-book-open')
                            ->badge(fn ($record) => $record?->schoolClasses()->count() ?: null)
                            ->schema([self::classManagementSection()]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->contained(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(self::tableColumns())
            ->filtersFormColumns(4)
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->color('info'),
                    EditAction::make()
                        ->color('warning'),
                    DeleteAction::make()
                        ->color('danger'),
                ])
                ->label('الإجراءات')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray')
                ->button(),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                ->label('حذف المحدد'),
            ])
            ->headerActions([
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة أول مدرسة')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->modifyQueryUsing(fn ($query) => $query->with(['product', 'media', 'teachers', 'schoolClasses']))
            ->poll('30s')
            ->deferLoading();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }

    protected static function quickStatsSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('إحصائيات سريعة')
            ->icon('heroicon-o-chart-bar')
            ->description('نظرة سريعة على بيانات المدرسة')
            ->schema([
                Forms\Components\Placeholder::make('teachers_count')
                    ->label('عدد المعلمين')
                    ->content(fn ($record) => $record?->teachers()->count() ?? 0)
                    ->extraAttributes(['class' => 'text-lg font-semibold text-primary-600']),

                Forms\Components\Placeholder::make('classes_count')
                    ->label('عدد الفصول')
                    ->content(fn ($record) => $record?->schoolClasses()->count() ?? 0)
                    ->extraAttributes(['class' => 'text-lg font-semibold text-success-600']),

                Forms\Components\Placeholder::make('images_count')
                    ->label('عدد الصور')
                    ->content(fn ($record) => $record?->getMedia('images')->count() ?? 0)
                    ->extraAttributes(['class' => 'text-lg font-semibold text-warning-600']),

                Forms\Components\Placeholder::make('school_age')
                    ->label('عمر المدرسة')
                    ->content(fn ($record) => $record?->founding_date ? (now()->year - $record->founding_date) . ' سنة' : 'غير محدد')
                    ->extraAttributes(['class' => 'text-lg font-semibold text-info-600']),
            ])
            ->columns(2)
            ->collapsible();
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
                    ->prefixIcon('heroicon-o-academic-cap')
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\Textarea::make('product.description')
                    ->required()
                    ->columnSpanFull()
                    ->label('وصف المدرسة')
                    ->rows(4)
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\TextInput::make('product.address')
                    ->required()
                    ->maxLength(255)
                    ->label('عنوان المدرسة')
                    ->prefixIcon('heroicon-o-map-pin')
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\SpatieMediaLibraryFileUpload::make('product.image')
                    ->collection('primary-image')
                    ->image()
                    ->preserveFilenames()
                    ->required()
                    ->label('صور المدرسة')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'])
                    ->helperText('قم بتحميل صورة واحدة أو أكثر للمدرسة (JPEG, PNG, GIF, WebP).')
                    ->reorderable()
                    ->imagePreviewHeight('120')
                    ->imageEditor()
                    ->panelLayout('grid')
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
                    ->hint('الاسم الكامل لمدير المدرسة')
                    ->prefixIcon('heroicon-o-user')
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\SpatieMediaLibraryFileUpload::make('manager-image')
                    ->collection('managers-images')
                    ->image()
                    ->preserveFilenames()
                    ->required()
                    ->label('صورة شخصية')
                    ->reorderable()
                    ->appendFiles()
                    ->imageEditor()
                    ->imageCropAspectRatio('3:4')
                    ->panelLayout('grid')
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
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
                    ->label('صور الخدمات والأنشطة')
                    ->columnSpan(2)
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9')
                    ->imageEditor()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'])
                    ->helperText('صور عالية الجودة للخدمات والأنشطة')
                    ->downloadable()
                    ->openable()
                    ->panelLayout('grid')
                    ->reorderable()
                    ->extraInputAttributes(['dir' => 'rtl']),
            ]);
    }

    protected static function teachingStaffSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('الهيئة التدريسية')
            ->icon('heroicon-o-users')
            ->description('إدارة معلمي المدرسة')
            ->collapsible()
            ->schema([
                Forms\Components\Repeater::make('teachers')
                    ->relationship('teachers')
                    ->label('')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('اسم المعلم')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-user')
                            ->extraInputAttributes(['dir' => 'rtl']),

                        Forms\Components\TextInput::make('job_title')
                            ->required()
                            ->label('المسمى الوظيفي')
                            ->maxLength(255)
                            ->prefixIcon('heroicon-o-briefcase')
                            ->extraInputAttributes(['dir' => 'rtl']),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                            ->collection('image')
                            ->image()
                            ->preserveFilenames()
                            ->label('صورة شخصية')
                            ->circleCropper()
                            ->imageCropAspectRatio('1:1')
                            ->downloadable()
                            ->openable()
                            ->extraInputAttributes(['dir' => 'rtl']),

                        Forms\Components\Actions::make([
                            FormAction::make('saveTeacher')
                                ->label('حفظ المعلم')
                                ->icon('heroicon-o-check-circle')
                                ->color('success')
                                ->action(function ($component, $get, $set) {

                                    $teacherData = [
                                        'name' => $get('name'),
                                        'job_title' => $get('job_title'),
                                        'school_id' => 1,
                                    ];

                                    // Save teacher independently
                                    Teacher::query()->create($teacherData);

                                    Notification::make()
                                        ->title('تم حفظ المعلم')
                                        ->success()
                                        ->send();
                                })
                                ->requiresConfirmation()
                                ->modalHeading('حفظ المعلم')
                                ->modalDescription('هل تريد حفظ بيانات هذا المعلم الآن؟ سيتم حفظه في قاعدة البيانات دون حفظ المدرسة.')
                                ->modalSubmitActionLabel('نعم، احفظ')
                                ->modalCancelActionLabel('إلغاء')
                        ])->fullWidth(),
                    ])
                    ->columns(2)
                    ->addActionLabel('إضافة معلم جديد')
                    ->itemLabel(fn (array $state): string => $state['name'] ?: 'معلم جديد')
                    ->collapsible()
                    ->cloneable()
                    ->grid(1)
                    ->defaultItems(0)
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
                            ->prefixIcon('heroicon-o-book-open')
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

                        Forms\Components\Select::make('teachers')
                            ->relationship(
                                name: 'teachers',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('school_id', $get('../../id'))->orWhere('school_id' , 1)
                            )
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('المعلمين')
                            ->columnSpanFull()
                            ->prefixIcon('heroicon-o-users')
                            ->required()
                            ->native(false)
                            ->extraInputAttributes(['dir' => 'rtl']),

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
                            ->panelLayout('grid')
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
                                if (isset($state['id'])) {
                                    $schoolClass = \App\Models\SchoolClass::find($state['id']);
                                    if ($schoolClass) {
                                        $schoolClass->teachers()->detach();
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
                    ->grid(1)
                    ->defaultItems(0)
                    ->collapsed(),
            ]);
    }

    protected static function tableColumns(): array
    {
        return [
            ImageColumn::make('primary_image')
                ->label('')
                ->getStateUsing(fn ($record) => $record->getFirstMediaUrl('primary-image'))
                ->square()
                ->size(60)
                ->defaultImageUrl(url('/images/placeholder-school.png'))
                ->extraAttributes(['class' => 'rounded-lg shadow-sm']),

            TextColumn::make('product.title')
                ->label('اسم المدرسة')
                ->sortable()
                ->searchable()
                ->icon('heroicon-o-academic-cap')
                ->iconColor('primary')
                ->color('primary')
                ->weight('medium')
                ->copyable()
                ->copyMessage('تم نسخ اسم المدرسة')
                ->limit(30)
                ->tooltip(function (TextColumn $column): ?string {
                    $state = $column->getState();
                    if (strlen($state) <= 30) {
                        return null;
                    }
                    return $state;
                }),

            TextColumn::make('working_duration')
                ->label('ساعات العمل')
                ->searchable()
                ->icon('heroicon-o-clock')
                ->iconColor('warning')
                ->badge()
                ->color('warning')
                ->tooltip('ساعات العمل'),

            TextColumn::make('founding_date')
                ->label('تاريخ التأسيس')
                ->sortable()
                ->icon('heroicon-o-calendar')
                ->iconColor('success')
                ->color('success')
                ->formatStateUsing(fn ($state) => $state . ' (' . (now()->year - $state) . ' سنة)')
                ->tooltip('عمر المدرسة'),

            TextColumn::make('manager')
                ->label('المدير')
                ->searchable()
                ->icon('heroicon-o-user')
                ->iconColor('info')
                ->color('info')
                ->copyable()
                ->limit(25),

            TextColumn::make('teachers_count')
                ->label('المعلمين')
                ->badge()
                ->getStateUsing(fn ($record) => $record->teachers()->count())
                ->color(fn ($state) => match (true) {
                    $state == 0 => 'danger',
                    $state > 0 && $state < 5 => 'warning',
                    $state >= 5 && $state < 15 => 'success',
                    $state >= 15 => 'primary',
                })
                ->icon(fn ($state) => $state > 0 ? 'heroicon-o-users' : 'heroicon-o-exclamation-triangle')
                ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query->withCount('teachers')->orderBy('teachers_count', $direction);
                }),

            TextColumn::make('classes_count')
                ->label('الفصول')
                ->badge()
                ->getStateUsing(fn ($record) => $record->schoolClasses()->count())
                ->color(fn ($state) => match (true) {
                    $state == 0 => 'danger',
                    $state > 0 && $state < 6 => 'warning',
                    $state >= 6 && $state < 16 => 'success',
                    $state >= 16 => 'primary',
                })
                ->icon(fn ($state) => $state > 0 ? 'heroicon-o-book-open' : 'heroicon-o-exclamation-triangle')
                ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query->withCount('schoolClasses')->orderBy('school_classes_count', $direction);
                }),

            TextColumn::make('created_at')
                ->label('تاريخ الإنشاء')
                ->dateTime('Y-m-d H:i')
                ->sortable()
                ->color('gray')
                ->size('sm')
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'product.title',
            'product.description',
            'product.address',
            'manager',
            'working_duration'
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->product->title;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'المدير' => $record->manager,
            'العنوان' => $record->product->address,
            'ساعات العمل' => $record->working_duration,
        ];
    }

    public static function getGlobalSearchResultActions(Model $record): array
    {
        return [
            Action::make('edit')
                ->url(static::getUrl('edit', ['record' => $record]))
                ->icon('heroicon-m-pencil-square')
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $count = static::getModel()::count();

        if ($count < 5) {
            return 'warning';
        } elseif ($count < 20) {
            return 'success';
        }

        return 'primary';
    }
}
