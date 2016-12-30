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

import { TRANSFORM_ITEMS } from '../constants/Menu';

export const buildMenuItems = items => ({
  type:     TRANSFORM_ITEMS,
  items,
  // TODO add real values
  authData: {
    is_logged_in: false,
    is_admin:     false
  }
});
