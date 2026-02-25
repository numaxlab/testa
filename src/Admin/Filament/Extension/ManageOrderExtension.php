<?php

namespace Testa\Admin\Filament\Extension;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Lunar\Admin\Support\Extending\ViewPageExtension;

class ManageOrderExtension extends ViewPageExtension
{
    public function headerActions(array $actions): array
    {
        return [
            ...$actions,
            $this->getUploadInvoiceAction(),
            $this->getDownloadInvoiceAction(),
            $this->getRemoveInvoiceAction(),
        ];
    }

    protected function getUploadInvoiceAction(): Action
    {
        return Action::make('upload_invoice')
            ->label('Subir factura')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('gray')
            ->form([
                FileUpload::make('invoice_file')
                    ->label('Archivo de factura')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                    ->directory('invoices')
                    ->disk('public')
                    ->required(),
            ])
            ->action(function (array $data, $record) {
                $path = $data['invoice_file'];

                $oldPath = $record->meta['invoice_path'] ?? null;
                if ($oldPath && $oldPath !== $path) {
                    Storage::disk('public')->delete($oldPath);
                }

                $meta = collect($record->meta ?? [])->merge([
                    'invoice_path' => $path,
                    'invoice_filename' => basename($path),
                ])->all();

                $record->meta = $meta;
                $record->save();

                Notification::make()
                    ->title('Factura subida correctamente')
                    ->success()
                    ->send();
            });
    }

    protected function getDownloadInvoiceAction(): Action
    {
        return Action::make('download_invoice')
            ->label('Descargar factura')
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->visible(fn($record) => filled($record->meta['invoice_path'] ?? null))
            ->url(fn($record) => Storage::disk('public')->url($record->meta['invoice_path']))
            ->openUrlInNewTab();
    }

    protected function getRemoveInvoiceAction(): Action
    {
        return Action::make('remove_invoice')
            ->label('Eliminar factura')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->visible(fn($record) => filled($record->meta['invoice_path'] ?? null))
            ->action(function ($record) {
                $path = $record->meta['invoice_path'] ?? null;

                if ($path) {
                    Storage::disk('public')->delete($path);
                }

                $meta = collect($record->meta ?? [])->except(['invoice_path', 'invoice_filename'])->all();
                $record->meta = $meta;
                $record->save();

                Notification::make()
                    ->title('Factura eliminada correctamente')
                    ->success()
                    ->send();
            });
    }

    public function extendAdditionalInfoSection(Component $section): Component
    {
        return $section->schema(function ($state) {
            $hiddenKeys = ['invoice_path', 'invoice_filename'];
            $items = collect($state ?? [])->except($hiddenKeys);

            if ($items->isEmpty()) {
                return [
                    TextEntry::make('no_additional_info')
                        ->hiddenLabel()
                        ->getStateUsing(fn() => __('lunarpanel::order.infolist.no_additional_info.label')),
                ];
            }

            return $items->map(function ($value, $key) {
                if (is_array($value)) {
                    return KeyValueEntry::make('meta_'.$key)->state($value);
                }

                return TextEntry::make('meta_'.$key)
                    ->state($value)
                    ->label($key)
                    ->copyable()
                    ->limit(50)
                    ->tooltip(function (TextEntry $component): ?string {
                        $state = $component->getState();
                        if (strlen($state) <= $component->getCharacterLimit()) {
                            return null;
                        }

                        return $state;
                    });
            })->values()->toArray();
        });
    }
}
