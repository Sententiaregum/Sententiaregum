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

import React, { PropTypes }                                           from 'react';
import { Field, reduxForm }                                           from 'redux-form';
import {
  FormGroup, FormControl,
  ControlLabel, Button,
  Alert, Radio
}                                                                     from 'react-bootstrap';
import createRecaptchaWrapper                                         from './createRecaptchaWrapper';
import siteKey                                                        from '../../../config/recaptcha';
import Success                                                        from './Success';
import { Suggestions }                                                from './Suggestions';
import HelpBlock                                                      from 'react-bootstrap/lib/HelpBlock';

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
  <FormGroup validationState={touched && (error) ? 'error' : null}>
    <ControlLabel>{label}</ControlLabel>
    <FormControl {...input} placeholder={label} type={type}/>
    <FormControl.Feedback />
    {
      error
        ? error['en'].map((msg, i) => <HelpBlock key={i}>{msg}</HelpBlock>)
        : null // @TODO generic language + translation
    }
  </FormGroup>;

/**
 * Custom Component builder for selectables
 *
 * @param input
 * @param label
 */
const dropDownComponent = ({ input, label }) =>
  <div >
    <b>{label}</b>
    <div onChange={e => input.onChange(e.target.value)}>
      <input type="radio" value="de" name="locale" /> Deutsch (DE) <br />
      <input type="radio" value="en" name="locale" defaultChecked="defaultChecked" /> English (EN) <br />
    </div>
  </div>;

const recaptchaComponent = createRecaptchaWrapper(siteKey);

/**
 * Presentational form component
 *
 * @param handleSubmit
 * @param name_suggestions
 * @param success
 */
let Form = ({ handleSubmit, name_suggestions, success }) =>
  <form onSubmit={handleSubmit}>
    <Suggestions suggestions={name_suggestions} />
    {success ? <Success /> : null}
    <Field component={customComponent} type="text" label="Username" name="username" />
    <Field component={customComponent} type="password" label="Password" name="password" />
    <Field component={customComponent} type="email" label="Email" name="email" />
    <Field component={dropDownComponent} label="Select Language" name="locale" />
    <Field component={recaptchaComponent} label="recaptcha" name="recaptchaHash" success={success} />
    <Button type="submit">Register!</Button>
  </form>;

export default Form = reduxForm({
  form: 'sign_up'
})(Form);

Form.propTypes = {
  handleSubmit:     PropTypes.func,
  name_suggestions: PropTypes.array,
  success:          PropTypes.bool.isRequired
};
