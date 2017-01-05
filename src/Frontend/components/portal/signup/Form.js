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

import React                                                   from 'react';
import { Field, reduxForm }                                    from 'redux-form';
import { FormGroup, FormControl,
        ControlLabel, Button,
        Alert, DropdownButton,
        MenuItem }                                            from 'react-bootstrap';
import { validation }                                          from './validation/FormValidation';
// import Recaptcha                                             from 'react-recaptcha';
// import siteKey                                               from '../../../config/recaptcha';

/**
 * Validation for the custom components
 * @param values
 */
const validate = values => validation(values);
// const callback = () => {};

/**
 * Custom Component builder for inputs
 *
 * @param input
 * @param label
 * @param type
 * @param touched
 * @param error
 */
const customComponent = ({ input, label, type, meta: { touched, error } }) =>
  <FormGroup>
    <ControlLabel>{label}</ControlLabel>
    <FormControl {...input} placeholder={label} type={type} />
    {touched && ((error && <Alert bsStyle="danger">{error}</Alert>))}
  </FormGroup>;

/**
 * Custom Component builder for selectables
 *
 * @param input
 * @param label
 * @param type
 * @param touched
 * @param error
 * @constructor
 */
//TODO: Get this to work
const DropDownComponent = ({ input, label, type, meta: { touched, error } }) =>
  <DropdownButton title={label}>
    <MenuItem>Deutsch (Deutschland)</MenuItem>
    <MenuItem>English (USA)</MenuItem>
  </DropdownButton>;

/**
 * Presentational component
 *
 * @param handleSubmit
 * @constructor
 */
// TODO: Figure out best way to implement the Recaptcha stuff in a presentational component.
// TODO: (You will somehow have to get the recaptcha crap in to the handleSubmit method, or you just dispatch it directly from the Form component)
// TODO: Name suggestions

let Form = ({ handleSubmit }) =>
  <form onSubmit={handleSubmit}>
    <div>
      <Field component={customComponent}    type="text"      label="Username"        name="username" />
      <Field component={customComponent}    type="password"  label="Password"        name="password" />
      <Field component={customComponent}    type="email"     label="Email"           name="email" />
      <Field component={DropDownComponent}                   label="Select Language" name="email" />
      <Button type="submit">Register!</Button>
    </div>
  </form>;

export default Form = reduxForm({
  form: 'sign_up',
  validate
})(Form);
