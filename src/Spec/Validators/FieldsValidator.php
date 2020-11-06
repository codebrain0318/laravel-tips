<?php
/**
 * Copyright 2020 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

declare(strict_types=1);

namespace LaravelJsonApi\Spec\Validators;

use LaravelJsonApi\Spec\Document;
use LaravelJsonApi\Spec\Translator;

class FieldsValidator
{

    private Translator $translator;

    /**
     * FieldsValidator constructor.
     *
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Validate a resource's fields (attributes + relationships).
     *
     * @param Document $document
     * @param \Closure $next
     * @return Document
     */
    public function validate(Document $document, \Closure $next): Document
    {
        $duplicates = collect((array) $document->get('data.attributes', []))
            ->intersectByKeys((array) $document->get('data.relationships', []))
            ->keys()
            ->map(fn($field) => $this->translator->resourceFieldExistsInAttributesAndRelationships($field));

        $document->errors()->push(...$duplicates);

        return $next($document);
    }
}
