/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { TRANSFORM_ITEMS }          from '../../constants/Menu';

export default (state = [], action) => {
  if (TRANSFORM_ITEMS === action.type) {
    const { items, authData } = action;
    return items.filter(item => !(
      'ROLE_ADMIN' === item.role && !authData.is_admin
      || item.logged_in && !authData.logged_in
      || item.portal && authData.logged_in
    ));
  }

  return state;
};
