<?php

declare(strict_types=1);
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our licensing model, this program can be used
 * under the terms of the GNU Affero General Public License, version 3.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission can be found at and in the LICENSE file you have received
 * along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore, any rights, title and interest in
 * our trademarks remain entirely with the shopware AG.
 */

namespace Shopware\Tests\Unit\Components\Theme;

use PHPUnit\Framework\TestCase;
use Shopware\Components\ShopwareReleaseStruct;
use Shopware\Components\Theme\PathResolver;
use Shopware\Kernel;
use Shopware\Models\Shop\Shop;
use Shopware\Models\Shop\Template;

class PathResolverTest extends TestCase
{
    private PathResolver $pathResolver;

    protected function setUp(): void
    {
        $releaseArray = (new Kernel('testing', true))->getRelease();

        $this->pathResolver = new PathResolver(
            '/my/root/dir',
            ['/my/root/dir/templates'],
            '/my/root/dir/template',
            '/my/root/dir/web/cache',
            new ShopwareReleaseStruct($releaseArray['version'], $releaseArray['version_text'], $releaseArray['revision'])
        );
    }

    public function testFiles(): void
    {
        $timestamp = '200000';
        $templateId = 5;
        $shopId = 4;
        $release = (new Kernel('testing', true))->getRelease();

        $templateMock = $this->createTemplateMock($templateId);
        $shopMock = $this->createShopMock($shopId, $templateMock);

        $filenameHash = $timestamp . '_' . md5($timestamp . $templateId . $shopId . $release['revision']);

        $expected = '/my/root/dir/web/cache/' . $filenameHash . '.css';
        static::assertSame($expected, $this->pathResolver->getCssFilePath($shopMock, $timestamp));

        $expected = '/my/root/dir/web/cache/' . $filenameHash . '.js';
        static::assertSame($expected, $this->pathResolver->getJsFilePath($shopMock, $timestamp));
    }

    private function createTemplateMock(int $templateId): Template
    {
        return $this->createConfiguredMock(Template::class, ['getId' => $templateId]);
    }

    private function createShopMock(int $shopId, Template $templateStub): Shop
    {
        return $this->createConfiguredMock(Shop::class, [
            'getMain' => null,
            'getId' => $shopId,
            'getTemplate' => $templateStub,
        ]);
    }
}
