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

import { jsdom } from 'jsdom';
import counterpart from 'counterpart';
import de from '../config/languages/de';
import en from '../config/languages/en';

counterpart.registerTranslations('de', de);
counterpart.registerTranslations('en', en);

const document   = jsdom();
global.document  = document;
global.window    = global.document.parentWindow;
global.navigator = window.navigator;
