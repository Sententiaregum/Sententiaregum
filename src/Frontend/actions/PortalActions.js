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
import AppDispatcher from '../dispatcher/AppDispatcher';
import Portal from '../constants/Portal';

/**
 * Action creator for portal logic.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
class PortalActions {
  /**
   * Processor for the registration.
   *
   * @param {Object.<string>} formData Data to submit.
   *
   * @returns {void}
   */
  registration(formData) {
    AccountWebAPIUtils.createAccount(
      formData,
      result => {
        AppDispatcher.dispatch({
          event: Portal.CREATE_ACCOUNT,
          result
        });
      },
      result => {
        AppDispatcher.dispatch({
          event:           Portal.ACCOUNT_VALIDATION_ERROR,
          errors:          result.errors,
          nameSuggestions: result['name_suggestions'] ? result['name_suggestions'] : []
        });
      }
    );
  }

  /**
   * Activation facade for the user.
   *
   * @param {string} username Username.
   * @param {string} key Activation key.
   *
   * @returns {void}
   */
  activate(username, key) {
    AccountWebAPIUtils.activate(
      username,
      key,
      () => AppDispatcher.dispatch({ event: Portal.ACTIVATE_ACCOUNT }),
      () => AppDispatcher.dispatch({ event: Portal.ACTIVATION_FAILURE })
    );
  }
}

export default new PortalActions();
