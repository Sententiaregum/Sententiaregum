/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import de          from './languages/de';
import en          from './languages/en';
import counterpart from 'counterpart';
import Locale      from '../util/http/Locale';

// null as parameter will make the locale be fetched from the cookie store.
// This is necessary since the cookie store is the only data source which contains
// the appropriate locale when the App will be bootstrapped.
Locale.setLocale(null);

// register translation files
counterpart.registerTranslations('de', de);
counterpart.registerTranslations('en', en);
