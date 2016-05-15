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

import React from 'react';
import FormControl from 'react-bootstrap/lib/FormControl';
import CompositeFormField from './CompositeFormField';

/**
 * Simple component for form fields.
 *
 * @param {Object} props The properties.
 *
 * @returns {React.Element} The markup of the single form field.
 */
const FormField = props => {
  const { name, type, value, errors, helper, ...settings } = props;

  return (
    <CompositeFormField name={name} errors={errors} helper={helper}>
      <FormControl name={name} type={type} onChange={helper.getChangeListener()} value={value} placeholder={helper.getTranslatedFormField(name)} {...settings} main={true} />
      <FormControl.Feedback />
    </CompositeFormField>
  );
};

FormField.propTypes = Object.assign({}, CompositeFormField.propTypes, {
  type:  React.PropTypes.string,
  value: React.PropTypes.string
});

export default FormField;
