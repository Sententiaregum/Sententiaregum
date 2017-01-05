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

import isEmail                                        from 'sane-email-validation';

/**
 * Validation helper for the Form component
 *
 * @param values
 */
export const validation = (values) => {

  const errors = {};

  if (!values.username) {
    errors.username = 'You are required to set a username!';
  }
  if (!values.password) {
    errors.password = 'You must set a password!';
  }
  if (!values.email) {
    errors.email = 'You must set a email address';
  } else if (!isEmail(values.email)) {
    errors.email = 'Invalid email address';
  }

  return errors
};
