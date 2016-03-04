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

import AccountWebAPIUtils from '../util/api/AccountWebAPIUtils';
import { CREATE_ACCOUNT, ACCOUNT_VALIDATION_ERROR, ACTIVATE_ACCOUNT, ACTIVATION_FAILURE, REQUEST_API_KEY, LOGIN_ERROR } from '../constants/Portal';

/**
 * Processor for the registration.
 *
 * @param {Object} formData The form data.
 *
 * @returns {Function} The action creator.
 */
export function registration(formData) {
  return dispatch => {
    AccountWebAPIUtils.createAccount(
      formData,
      result => dispatch(CREATE_ACCOUNT, result),
      result => dispatch(
        ACCOUNT_VALIDATION_ERROR,
        { errors: result.errors, nameSuggestions: result['name_suggestions'] ? result['name_suggestions'] : [] }
      )
    );
  };
}

/**
 * Activation handler.
 *
 * @param {String} username The user to activate.
 * @param {String} key      The activation key.
 *
 * @returns {Function} The action creator.
 */
export function activate(username, key) {
  return dispatch => {
    AccountWebAPIUtils.activate(
      username,
      key,
      () => dispatch(ACTIVATE_ACCOUNT, {}),
      () => dispatch(ACTIVATION_FAILURE, {})
    );
  };
}

/**
 * Authenticates against the api.
 *
 * @param {string} username Name of the user.
 * @param {string} password Password of the user.
 *
 * @returns {Function} The action creator.
 */
export function authenticate(username, password) {
  return dispatch => {
    AccountWebAPIUtils.requestApiKey(
      username,
      password,
      data => dispatch(LOGIN_ERROR, { message: data.message }),
      data => dispatch(REQUEST_API_KEY, { apiKey: data.apiKey, roles: data.roles, username: data.username })
    );
  };
}
