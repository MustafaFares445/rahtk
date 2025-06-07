<?php

namespace App\Filament\Resources;

use App\Models\School;
use App\Enums\ProductTypes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use App\Filament\Resources\SchoolResource\Pages;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                self::schoolInformationSection(),
                self::schoolAttributesSection(),
                self::managementProfileSection(),

                Forms\Components\Tabs::make('More Details')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Services & Activities')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([self::servicesActivitiesSection()]),

                        Forms\Components\Tabs\Tab::make('Teaching Staff')
                            ->icon('heroicon-o-users')
                            ->schema([self::teachingStaffSection()]),

                        Forms\Components\Tabs\Tab::make('Class Management')
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
                // Filters can be added here
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
        return Forms\Components\Section::make('School Information')
            ->icon('heroicon-o-information-circle')
            ->description('Basic information about the school')
            ->collapsible()
            ->relationship('product')
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('School Title')
                    ->helperText('Enter the official name of the school.'),

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->label('School Description'),

                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255)
                    ->label('School Address'),

                Forms\Components\Hidden::make('type')
                    ->default(ProductTypes::SCHOOL->value),

                Forms\Components\SpatieMediaLibraryFileUpload::make('video')
                    ->collection('videos')
                    ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv'])
                    ->preserveFilenames()
                    ->label('School Video')
                    ->helperText('Upload a video file (MP4, QuickTime, AVI, WMV) for the class.'),

                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                    ->collection('images')
                    ->image()
                    ->multiple()
                    ->preserveFilenames()
                    ->required()
                    ->label('School Images')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->helperText('Upload one or more images for the class (JPEG, PNG, GIF, WebP).')
                    ->reorderable()
                    ->imagePreviewHeight('100'),
            ]);
    }

    protected static function schoolAttributesSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('School Attributes')
            ->icon('heroicon-o-bookmark')
            ->description('Key characteristics and historical information')
            ->collapsible()
            ->columns(2)
            ->schema([
                Forms\Components\Textarea::make('quate')
                    ->columnSpanFull()
                    ->label('School Motto')
                    ->placeholder('Enter an inspiring quote or motto for your school...')
                    ->rows(3)
                    ->maxLength(500)
                    ->helperText('A short, memorable phrase that represents your school')
                    ->extraInputAttributes(['class' => 'prose max-w-full']),

                Forms\Components\TextInput::make('working_duration')
                    ->required()
                    ->maxLength(255)
                    ->label('Operating Hours')
                    ->placeholder('8:00 AM - 4:00 PM')
                    ->hint('Daily school hours')
                    ->prefixIcon('heroicon-o-clock'),

                Forms\Components\DatePicker::make('founding_date')
                    ->required()
                    ->label('Established Date')
                    ->displayFormat('F j, Y')
                    ->native(false)
                    ->maxDate(now())
                    ->hint('When was the school founded?')
                    ->prefixIcon('heroicon-o-cake'),

                Forms\Components\TextInput::make('address')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull()
                    ->label('Full Address')
                    ->placeholder('123 Education Street, Knowledge City')
                    ->hint('Physical location of the school')
                    ->prefixIcon('heroicon-o-map-pin'),
            ]);
    }

    protected static function managementProfileSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Management Profile')
            ->icon('heroicon-o-user-circle')
            ->description('Primary contact and leadership information')
            ->collapsible()
            ->schema([
                Forms\Components\TextInput::make('manager')
                    ->required()
                    ->maxLength(255)
                    ->label('Manager Name')
                    ->placeholder('علي محمد')
                    ->columnSpan(['sm' => 1])
                    ->hint('Full name of the school manager')
                    ->prefixIcon('heroicon-o-user')
                    ->extraInputAttributes(['dir' => 'rtl']),

                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                    ->collection('managers-images')
                    ->image()
                    ->preserveFilenames()
                    ->required()
                    ->label('Profile Photo')
                    ->columnSpan(['md' => 1])
                    ->reorderable()
                    ->appendFiles()
                    ->imageEditor()
                    ->imageCropAspectRatio('3:4')
                    ->panelLayout('grid')
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->helperText('Upload professional portrait photos (3:4 ratio recommended)')
                    ->hint('Max 5MB per image • JPEG, PNG, or WebP'),

                Forms\Components\Textarea::make('manager_description')
                    ->required()
                    ->columnSpanFull()
                    ->label('Professional Profile')
                    ->rows(6)
                    ->placeholder('وصف دور المدير، الخبرة، والمؤهلات...')
                    ->helperText('Brief professional biography (200-300 words recommended)')
                    ->extraInputAttributes([
                        'class' => 'prose max-w-full',
                        'dir' => 'rtl',
                        'style' => 'text-align: right;'
                    ]),
            ]);
    }

    protected static function servicesActivitiesSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Services & Activities')
            ->icon('heroicon-o-clipboard-document-list')
            ->description('Manage services and activities offered by the school')
            ->collapsible()
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                    ->collection('services-images')
                    ->image()
                    ->preserveFilenames()
                    ->required()
                    ->label('Profile Photo')
                    ->columnSpan(2)
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('1:1')
                    ->imageEditor()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                    ->helperText('High quality portrait photo (1:1 aspect ratio recommended)')
                    ->downloadable()
                    ->openable(),
            ]);
    }

    protected static function teachingStaffSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Teaching Staff')
            ->description('Manage your school\'s teaching team')
            ->icon('heroicon-o-users')
            ->collapsible()
            ->schema([
                Forms\Components\Repeater::make('school_teachers')
                    ->relationship('teachers')
                    ->label('Teachers List')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->label('Full Name')
                            ->placeholder('Dr. Sarah Johnson'),

                        Forms\Components\TextInput::make('job_title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->label('Position/Role')
                            ->placeholder('Math Department Head'),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                            ->collection('teachers-images')
                            ->image()
                            ->preserveFilenames()
                            ->required()
                            ->label('Profile Photo')
                            ->columnSpan(2)
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageEditor()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                            ->helperText('High quality portrait photo (1:1 aspect ratio recommended)')
                            ->downloadable()
                            ->openable(),

                        Forms\Components\Hidden::make('school_id')
                            ->default(fn ($livewire) => $livewire->data['id']),
                    ])
                    ->columns(2)
                    ->addActionLabel('+ Add New Teacher')
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->cloneable()
                    ->defaultItems(1)
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'New Teacher')
                    ->grid(2)
                    ->collapsed()
                    ->helperText('Add all teachers with their details and photos'),
            ]);
    }

    protected static function classManagementSection(): Forms\Components\Section
    {
        return Forms\Components\Section::make('Class Management')
            ->description('Organize school classes with detailed information')
            ->icon('heroicon-o-book-open')
            ->collapsible()
            ->schema([
                Forms\Components\Repeater::make('school_classes')
                    ->relationship('schoolClasses')
                    ->label('Class List')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(1)
                            ->label('Class Name'),

                        Forms\Components\Select::make('type')
                            ->required()
                            ->options([
                                'initial' => 'Initial Education',
                                'principal' => 'Primary Education',
                                'secondary' => 'Secondary Education'
                            ])
                            ->columnSpan(1)
                            ->label('Education Level')
                            ->native(false),

                        Forms\Components\Select::make('teachers')
                            ->relationship('teachers', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull()
                            ->label('Assigned Teachers')
                            ->createOptionAction(
                                fn ($action) => $action->modalHeading('Create New Teacher')
                            )
                            ->createOptionForm([
                                Forms\Components\Section::make('Teacher Information')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Full Name'),

                                        Forms\Components\TextInput::make('job_title')
                                            ->required()
                                            ->maxLength(255)
                                            ->label('Position'),

                                        Forms\Components\SpatieMediaLibraryFileUpload::make('image')
                                            ->collection('teachers-images')
                                            ->image()
                                            ->preserveFilenames()
                                            ->required()
                                            ->columnSpanFull()
                                            ->label('Profile Photo')
                                            ->imageEditor()
                                            ->imageCropAspectRatio('1:1'),

                                        Forms\Components\Hidden::make('school_id')
                                            ->default(fn ($livewire) => $livewire->data['id']),
                                    ]),
                            ])
                            ->hint('Select or add teachers for this class'),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('video')
                            ->collection('videos')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-ms-wmv'])
                            ->preserveFilenames()
                            ->columnSpanFull()
                            ->label('Class Video')
                            ->downloadable()
                            ->openable()
                            ->helperText('Upload a class introduction or demonstration video (max 50MB)')
                            ->hint('MP4, MOV, AVI, or WMV format'),

                        Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                            ->collection('images')
                            ->image()
                            ->multiple()
                            ->preserveFilenames()
                            ->required()
                            ->columnSpanFull()
                            ->label('Class Gallery')
                            ->reorderable()
                            ->appendFiles()
                            ->imageResizeMode('cover')
                            ->imageEditor()
                            ->helperText('Upload classroom photos, student work, or activities')
                            ->hint('JPEG, PNG, GIF, or WebP format')
                            ->directory('class-gallery'),
                    ])
                    ->columns(2)
                    ->addActionLabel('+ Add New Class')
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->cloneable()
                    ->itemLabel(fn (array $state): ?string => $state['name'] ? "{$state['name']}" : 'New Class')
                    ->grid(2)
                    ->defaultItems(1)
                    ->collapsed(),
            ]);
    }

    protected static function tableColumns(): array
    {
        return [
            ImageColumn::make('images')
                ->label('Image')
                ->square()
                ->stacked()
                ->getStateUsing(fn ($record) => $record->getFirstMedia('images')?->getUrl())
                ->size(50),

            Tables\Columns\TextColumn::make('product.title')
                ->label('Title')
                ->sortable()
                ->icon('heroicon-o-academic-cap')
                ->color('primary'),

            Tables\Columns\TextColumn::make('working_duration')
                ->searchable()
                ->icon('heroicon-o-clock')
                ->tooltip('Working Duration'),

            Tables\Columns\TextColumn::make('founding_date')
                ->date()
                ->sortable()
                ->icon('heroicon-o-calendar')
                ->color('success'),

            Tables\Columns\TextColumn::make('address')
                ->searchable()
                ->icon('heroicon-o-map-pin')
                ->tooltip('School Address'),

            Tables\Columns\TextColumn::make('manager')
                ->searchable()
                ->icon('heroicon-o-user')
                ->color('warning'),

            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),

            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }
}