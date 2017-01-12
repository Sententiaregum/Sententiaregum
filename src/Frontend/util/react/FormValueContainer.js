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

/**
 * Container to store form values.
 *
 * @author Maximilian Bosch <maximilian@mbosch.me>
 * @internal Internal utility for the form library.
 */
export default class FormValueContainer {
  /**
   * Persists form values into the local_storage.
   *
   * @param {String} alias The alias of the form.
   * @param {String} data  The form data.
   *
   * @returns {void}
   */
  persistFormValue(alias, data) {
    localStorage.setItem(alias, data);
  }

  /**
   * Finds a form value in the local_storage.
   *
   * @param {String} alias The alias.
   *
   * @returns {String} The data
   */
  getFormValueForAlias(alias) {
    return localStorage.getItem(alias);
  }

  /**
   * Purges form values.
   *
   * @param {String} aliasPrefix The alias to remove.
   *
   * @returns {void}
   */
  purge(aliasPrefix) {
    Object.keys(localStorage).forEach(index => {
      if (-1 !== index.indexOf(aliasPrefix)) {
        localStorage.removeItem(index);
      }
    });
  }
}
