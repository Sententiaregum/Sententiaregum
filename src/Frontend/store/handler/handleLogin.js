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

import Locale from '../../util/http/LocaleService';
import counterpart from 'counterpart';

/**
 * Handler which updates the locale.
 *
 * @returns {Object} The updated locale.
 */
export default () => {
  const locale = Locale.getLocale();
  if (locale !== counterpart.getLocale()) {
    counterpart.setLocale(locale);
  }
  return { locale };
};
