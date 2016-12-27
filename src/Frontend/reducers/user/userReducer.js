/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {
    CREATE_ACCOUNT,
    ACTIVATE_ACCOUNT,
    REQUEST_API_KEY,
    LOGOUT
}                                   from '../../constants/Portal';
import initialState                 from '../../config/initialState';

const userReducer = (state = initialState, action) => {
  switch (action.type) {
  case CREATE_ACCOUNT:
    return state;

  case ACTIVATE_ACCOUNT:
    return state;

  case REQUEST_API_KEY:
    return state;

  case LOGOUT:
    return state;

  default:
    return state;
  }
};

export default userReducer();
