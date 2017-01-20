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

import React, { PropTypes }     from 'react';
import Translate                from 'react-translate-component';
import isEmail                  from 'sane-email-validation';

/**
 * Validation helper for the Form component
 *
 * @param values
 */
export const validation = (values) => {

  const errors = {};

  if (!values.username) {
    errors.username = <Translate content="pages.portal.create_account.validation_errors.username" />;
  }
  if (!values.password) {
    errors.password = <Translate content="pages.portal.create_account.validation_errors.password" />;
  }
  if (!values.email) {
    errors.email = <Translate content="pages.portal.create_account.validation_errors.email" />;
  } else if (!isEmail(values.email)) {
    errors.email = <Translate content="pages.portal.create_account.validation_errors.invalid_email" />;
  }

  return errors;
};
