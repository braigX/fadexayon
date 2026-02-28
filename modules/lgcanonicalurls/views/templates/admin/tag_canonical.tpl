{**
 * Copyright 2022 LÍNEA GRÁFICA E.C.E S.L.
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.apache.org/licenses/LICENSE-2.0
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *}

{if isset($lgcanonical_url) && $lgcanonical_url}
    <link rel="canonical" href="{$lgcanonical_url|escape:'quotes':'UTF-8'}" />
{/if}

{foreach from=$lgcanonical_alternative_langs item=pageUrl key=code}
    <link rel="alternate" href="{$pageUrl|escape:'htmlall':'UTF-8'}" hreflang="{$code|escape:'htmlall':'UTF-8'}">
{/foreach}
