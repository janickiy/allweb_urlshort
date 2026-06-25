<?php

namespace Tests\Unit;

use App\DTO\LinkData;
use App\DTO\SettingData;
use Tests\TestCase;

class DataTransferObjectTest extends TestCase
{
    public function test_dto_keeps_only_allowed_model_fields(): void
    {
        $dto = LinkData::fromArray([
            'url' => 'https://example.com',
            'alias' => 'example',
            'user_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            '_token' => 'ignored',
            'submit' => 'ignored',
            'multi_link' => true,
        ]);

        $this->assertSame([
            'user_id' => 1,
            'alias' => 'example',
            'url' => 'https://example.com',
        ], $dto->toArray());
    }

    public function test_dto_preserves_allowed_null_values_for_updates(): void
    {
        $dto = SettingData::fromArray([
            'value' => null,
            '_token' => 'ignored',
        ]);

        $this->assertSame(['value' => null], $dto->toArray());
    }
}
