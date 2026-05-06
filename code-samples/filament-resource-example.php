<?php

/**
 * Generic Filament v3 Resource — BlogPost example.
 *
 * Demonstrates the patterns used across all admin resources in tekavogtil:
 *
 *  ✓ Bilingual text fields (NO + EN) in multi-column section layouts
 *  ✓ Slug field with unique validation (ignores self on edit)
 *  ✓ Select with badge colour mapping
 *  ✓ TagsInput for JSON array columns (requires array cast on model)
 *  ✓ Toggle for boolean visibility/published flag
 *  ✓ Reorderable table rows with sort_order column
 *  ✓ Ternary filter for boolean columns
 *  ✓ Norwegian navigation labels
 *
 * This uses a generic "BlogPost" model — the same structural patterns
 * are used in the tekavogtil admin panel for managing services, clients,
 * projects, invoices, and inquiries.
 *
 * ─── Model cast required for TagsInput ───────────────────────────────────
 *
 * In app/Models/BlogPost.php, add:
 *
 *   protected $casts = [
 *       'tags' => 'array',
 *   ];
 *
 * Without this cast, Filament's TagsInput stores a JSON string instead of
 * a PHP array, and reading back produces wrong output.
 *
 * ─── Migration ───────────────────────────────────────────────────────────
 *
 * Schema::create('blog_posts', function (Blueprint $table) {
 *     $table->id();
 *     $table->string('title_no');
 *     $table->string('title_en');
 *     $table->string('slug')->unique();
 *     $table->text('body_no');
 *     $table->text('body_en');
 *     $table->string('category');
 *     $table->json('tags')->nullable();
 *     $table->unsignedInteger('sort_order')->default(0);
 *     $table->boolean('is_published')->default(false);
 *     $table->timestamps();
 * });
 *
 * ─────────────────────────────────────────────────────────────────────────
 */

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    // Norwegian labels for the admin panel navigation
    protected static ?string $navigationLabel = 'Blogginnlegg';
    protected static ?string $modelLabel = 'Innlegg';
    protected static ?string $pluralModelLabel = 'Innlegg';

    // Sort position in the sidebar navigation
    protected static ?int $navigationSort = 1;

    // Optional: group resources in the sidebar
    // protected static ?string $navigationGroup = 'Innhold';

    // ─────────────────────────────────────────────────────────────────────
    // FORM — create / edit
    // ─────────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // Section: Bilingual titles + slug (3-column grid)
                Forms\Components\Section::make('Tittel og slug')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('title_no')
                            ->label('Tittel (norsk)')
                            ->required()
                            ->maxLength(200),

                        Forms\Components\TextInput::make('title_en')
                            ->label('Title (English)')
                            ->required()
                            ->maxLength(200),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            // ignoreRecord: true ensures uniqueness validation
                            // passes when editing an existing record's own slug
                            ->unique(ignoreRecord: true)
                            ->maxLength(200),
                    ]),

                // Section: Bilingual body content
                Forms\Components\Section::make('Innhold')
                    ->schema([
                        Forms\Components\Textarea::make('body_no')
                            ->label('Innhold (norsk)')
                            ->rows(6)
                            ->required(),

                        Forms\Components\Textarea::make('body_en')
                            ->label('Content (English)')
                            ->rows(6)
                            ->required(),
                    ]),

                // Section: Category select + sort order (2-column grid)
                Forms\Components\Section::make('Kategori og rekkefølge')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'news'     => 'Nyheter',
                                'tutorial' => 'Opplæring',
                                'update'   => 'Oppdatering',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Rekkefølge')
                            ->numeric()
                            ->default(0),
                    ]),

                // Section: Tags — stored as JSON array (requires 'array' cast on model)
                Forms\Components\Section::make('Tagger')
                    ->schema([
                        Forms\Components\TagsInput::make('tags')
                            ->label('Tagger')
                            ->placeholder('Legg til tagg...')
                            ->splitKeys(['Tab', 'Enter']),
                    ]),

                // Section: Visibility toggle
                Forms\Components\Section::make('Synlighet')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Publisert på nettsiden')
                            ->default(false),
                    ]),
            ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // TABLE — list view
    // ─────────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            // Default sort by the sort_order column
            ->defaultSort('sort_order')
            // Allow drag-reordering of rows (updates sort_order automatically)
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('title_no')
                    ->label('Tittel')
                    ->searchable()
                    ->sortable(),

                // Badge column — colour maps to category value
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'news'     => 'primary',
                        'tutorial' => 'success',
                        'update'   => 'info',
                        default    => 'gray',
                    }),

                // Boolean column — renders as green check / red X icon
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publisert')
                    ->boolean(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
            ])
            ->filters([
                // Dropdown filter by category
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'news'     => 'Nyheter',
                        'tutorial' => 'Opplæring',
                        'update'   => 'Oppdatering',
                    ]),

                // Three-state filter: All / Yes / No
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Publisert'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Rediger'),
            ])
            // No bulk actions — intentional for this resource
            ->bulkActions([]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PAGES — standard CRUD routes
    // ─────────────────────────────────────────────────────────────────────

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit'   => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
