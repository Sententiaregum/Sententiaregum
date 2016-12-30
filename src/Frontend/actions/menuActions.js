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

export const buildMenuItems = items => (dispatch, state) => dispatch({
  type:     TRANSFORM_ITEMS,
  items,
  authData: {
    logged_in: state().user.security.authenticated,
    is_admin:  false // TODO implement better role handling here
  }
});
