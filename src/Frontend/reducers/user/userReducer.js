/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {
  CREATE_ACCOUNT,
  CREATE_FAIL,
  ACTIVATE_ACCOUNT,
  REQUEST_API_KEY,
  LOGOUT
}                                   from '../../constants/Portal';
import { combineReducers }          from 'redux';
/*
 NOTE: each of these reducers is responsible for a certain sub-tree of the application's state.
 The `registration`, `activation` and `authentication` reducers are specific for their feature (see component structure for more information),
 but the `security` reducer contains security information about the application and can be composed to several user-specific actions.

 The app profile requires the following keys if initialized:
 - `apiKey`
 - `username`
 - `isAdmin`
 - `locale`
 */
const security = (state = { authenticated: false, appProfile: {} }, action) => {
  // TODO implement security related actions
  return state;
};

const registration = (state = { success: false, name_suggestions: [], id: null }, action) => {

  if (action.type === CREATE_ACCOUNT) {
    const newState = {
      id:      action.payload.id,
      success: true
    };

    return Object.assign({}, state, newState);
  }

  if (action.type === CREATE_FAIL) {
    const newState = {
      success:          false,
      name_suggestions: action.payload.name_suggestions
    };

    return Object.assign({}, state, newState);
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
