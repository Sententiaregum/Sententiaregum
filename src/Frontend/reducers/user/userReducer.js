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
import { combineReducers }          from 'redux';

// NOTE: each of these reducers is responsible for a certain sub-tree of the application's state.
// The `registration`, `activation` and `authentication` reducers are specific for their feature (see component structure for more information),
// but the `security` reducer contains security information about the application and can be composed to several user-specific actions.

const security = (state = { authenticated: false, appProfile: {} }, action) => {
  // TODO implement security related actions
  return state;
};

const registration = (state = { success: false, errors: {}, name_suggestions: [], id: null }, action) => {
  if (action.type === CREATE_ACCOUNT) {
    // TODO implement state reduction for user creation
  }
  return state;
};

const activation = (state = { success: false }, action) => {
  if (action.type === ACTIVATE_ACCOUNT) {
    // TODO implement state reduction for user activation
  }
  return state;
};

// TODO possibly implement better state for login/logout
const authentication = (state = { success: false, message: null }, action) => {
  if (action.type === REQUEST_API_KEY) {
    // TODO implement state reduction for user login
  }
  if (action.type === LOGOUT) {
    // TODO implement state reduction for user logout
  }
  return state;
};

export default combineReducers({
  security,
  registration,
  activation,
  authentication
});
