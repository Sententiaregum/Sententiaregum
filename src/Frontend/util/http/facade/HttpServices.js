/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import ApiKeyService from '../ApiKeyService';
import CookieFactory from '../CookieFactory';
import LocaleService from '../LocaleService';

const CookieInstance = new CookieFactory('undefined' === typeof window ? null : window);
export var ApiKey    = new ApiKeyService(CookieInstance);
export var Locale    = new LocaleService('en', CookieInstance);
