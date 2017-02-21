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

import counterpart from 'counterpart';

/**
 * Handler which updates the locale.
 *
 * @param {String}  locale  The new locale.
 * @param {Object}  prev    The previous state.
 * @param {boolean} success Whether or not the login successed.
 *
 * @returns {Object} The updated locale.
 */
export default ({ locale, success }, prev) => {
  if (locale !== counterpart.getLocale() && success) {
    counterpart.setLocale(locale);
  }
  return success ? Object.assign({}, prev, { current: { locale } }) : prev;
};
