<?php

declare(strict_types=1);

namespace Symfinity\PrivacySettingsBundle\Attribute;

final class MarkupDeclarationScanner
{
    /**
     * @return list<array{attribute:string, status:string}>
     */
    public function scan(string $markup): array
    {
        preg_match_all('/\s(data-[a-z0-9-]+)\s*=/', $markup, $matches);

        $result = [];
        $seen = array_unique($matches[1] ?? []);
        foreach ($seen as $attribute) {
            $status = StrictAttributeValidator::CANONICAL_ATTRIBUTE === $attribute ? 'accepted' : 'rejected';
            $result[] = [
                'attribute' => $attribute,
                'status' => $status,
            ];
        }

        return $result;
    }
}
