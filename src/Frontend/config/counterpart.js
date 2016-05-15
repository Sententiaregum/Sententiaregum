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

import de from './languages/de';
import en from './languages/en';
import counterpart from 'counterpart';
import Locale from '../util/http/LocaleService';

Locale.setLocale(null);
counterpart.registerTranslations('de', de);
counterpart.registerTranslations('en', en);

