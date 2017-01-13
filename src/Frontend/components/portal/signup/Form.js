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

import React                                                       from 'react';
import { Field, reduxForm }                                        from 'redux-form';
import {
  FormGroup, FormControl,
  ControlLabel, Button,
  Alert, Radio
}                                                                 from 'react-bootstrap';
import { validation }                                             from './validation/FormValidation';
import Recaptcha                                                  from 'react-recaptcha';
import siteKey                                                    from '../../../config/recaptcha';

/**
 * Validation for the custom components
 * @param values
 */
const validate = values => validation(values);

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
 */
const dropDownComponent = ({ input, label }) =>
  <div>
    <b>{label}</b>
    <FormGroup>
      <Radio inline inputRef={ref => { input.onChange(ref); }}>
          Deutsch
       </Radio>
      <Radio checked inline inputRef={ref => { input.onChange(ref); }}>
          English
      </Radio>
    </FormGroup>
  </div>;

/**
 * Custom Component for recaptcha
 *
 * @param input
 */
const recaptchaComponent = ({ input }) =>
  <Recaptcha
    sitekey={siteKey}
    render='explicit'
    onloadCallback={() => {}}
    verifyCallback={res => input.onChange(res)}
  />;


/**
 * Presentational component
 *
 * @param handleSubmit
 * @constructor
 */
// TODO: Name suggestions
let Form = ({ handleSubmit }) =>
  <form onSubmit={handleSubmit}>
    <Field component={customComponent} type="text"     label="Username"        name="username" />
    <Field component={customComponent} type="password" label="Password"        name="password" />
    <Field component={customComponent} type="email"    label="Email"           name="email" />
    <Field component={dropDownComponent}               label="Select Language" name="locale" />
    <Field component={recaptchaComponent}              label="recaptcha"       name="recaptchaHash" />
    <Button type="submit">Register!</Button>
  </form>;

export default Form = reduxForm({
  form: 'sign_up',
  validate
})(Form);
