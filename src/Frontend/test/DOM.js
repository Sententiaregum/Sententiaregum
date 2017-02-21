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

import { jsdom } from 'jsdom';
import { LocalStorage } from 'node-localstorage';

global.window       = jsdom('<html><head></head><body></body></html>').defaultView;
global.document     = window.document;
global.navigator    = window.navigator;
global.localStorage = new LocalStorage('./node-emulator');

require.extensions['.css'] = () => {
};
