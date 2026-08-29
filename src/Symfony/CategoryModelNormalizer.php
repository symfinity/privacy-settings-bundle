<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Symfony;

use Symfinity\PrivacySettingsBundle\Domain\PrivacyCategory;
use Symfinity\PrivacySettingsBundle\Domain\PrivacySettingsException;

final class CategoryModelNormalizer
{
    /**
     * @param list<array{id:string,label:string,default_state:string,description?:string}> $rawCategories
     *
     * @return list<PrivacyCategory>
     */
    public function normalize(array $rawCategories): array
    {
        $categories = [];
        $known = [];

        foreach ($rawCategories as $raw) {
            if (in_array($raw['id'], $known, true)) {
                throw PrivacySettingsException::duplicateCategoryId($raw['id']);
            }

            $known[] = $raw['id'];
            $categories[] = new PrivacyCategory(
                id: $raw['id'],
                label: $raw['label'],
                defaultState: $raw['default_state'],
                description: $raw['description'] ?? '',
            );
        }

        return $categories;
    }
}
