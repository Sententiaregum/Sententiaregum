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

import { store, subscribe }    from 'sententiaregum-flux-container';
import filterItemsByVisibility from './handler/filterItemsByVisibility';
import { TRANSFORM_ITEMS }     from '../constants/Menu';

export default store({
  [TRANSFORM_ITEMS]: subscribe(subscribe.chain()(filterItemsByVisibility))
}, { items: [] });
