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

global.window    = jsdom('<html><head></head><body></body></html>').defaultView;
global.document  = window.document;
global.navigator = window.navigator;
