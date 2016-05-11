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

import FluxEventHubStore from './FluxEventHubStore';
import Portal from '../constants/Portal';

/**
 * Store containing results of registration store.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com.>
 */
class RegistrationStore extends FluxEventHubStore {
  /**
   * Constructor.
   *
   * @returns {void}
   */
  constructor() {
    super();
    this.errors = this.suggestions = [];
  }

  /**
   * Adds the registration errors to the store.
   *
   * @param {Array.<string>} errors      Errors of the registration data.
   * @param {Array.<string>} suggestions Name suggestions.
   *
   * @returns {void}
   */
  addErrors(errors, suggestions) {
    this.errors = errors;
    if (suggestions) {
      this.suggestions = suggestions;
    } else {
      this.suggestions = [];
    }

    this.emitChange('CreateAccount.Error');
  }

  /**
   * Getter for the occurred errors.
   *
   * @returns {Array.<string>} All validation errors.
   */
  getErrors() {
    return this.errors;
  }

  /**
   * Getter for the suggestions.
   *
   * @returns {Array.<string>} The suggestions.
   */
  getSuggestions() {
    return this.suggestions;
  }

  /**
   * Success handler.
   *
   * @returns {void}
   */
  onSuccess() {
    this.errors      = [];
    this.suggestions = [];
    this.emitChange('CreateAccount.Success');
  }

  /**
   * @inheritdoc
   */
  getSubscribedEvents() {
    return [
      { name: Portal.CREATE_ACCOUNT, callback: this.onSuccess.bind(this) },
      { name: Portal.ACCOUNT_VALIDATION_ERROR, callback: this.addErrors.bind(this), params: ['errors', 'nameSuggestions'] }
    ];
  }
}

const store = new RegistrationStore();
store.init();

export default store;
