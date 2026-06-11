<?php

namespace App\Domain\Setting;

use Helix\Database\Repository;

class SettingRepository extends Repository
{
    protected string $table = 'settings';
    protected string $entityClass = Setting::class;

    public function get(string $key, mixed $default = null): mixed
    {
        $setting = $this->findOneBy('key', $key);
        return $setting ? $setting['value'] : $default;
    }

    public function set(string $key, mixed $value): void
    {
        $existing = $this->findOneBy('key', $key);
        if ($existing) {
            $this->update($existing['id'], ['value' => (string) $value]);
        } else {
            $this->create(['key' => $key, 'value' => (string) $value]);
        }
    }

    public function getAllAsArray(): array
    {
        $settings = $this->findAll();
        $result = [];
        foreach ($settings as $s) {
            $result[$s['key']] = $s['value'];
        }
        return $result;
    }
}
