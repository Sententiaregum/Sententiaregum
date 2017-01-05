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

import React                                          from 'react';
import {Field, reduxForm}                             from 'redux-form';
import {FormGroup, FormControl, ControlLabel, Button} from 'react-bootstrap';
import {validation}                                   from './validation/FormValidation'

/**
 * Validation
 *
 * @param values
 */
const validate = values => validation(values);

/**
 * Field builder and add bootstrap
 *
 * @param fields
 */
const customComponent = field =>
    <FormGroup>
      <ControlLabel>{field.input.placeholder}</ControlLabel>
      <FormControl {...field.input} />
      {field.error && field.touched && <span>{field.error}</span>}
    </FormGroup>;

/**
 * Presentational component
 *
 * @param handleSubmit
 * @constructor
 */
let Form = ({handleSubmit}) =>
  <div>
    <form onSubmit={handleSubmit}>
      <div>
        <Field component={customComponent} placeholder="Username" name="username"/>
        <Field component={customComponent} placeholder="Password" name="password"/>
        <Field component={customComponent} placeholder="Email" name="email"/>
        <Button type="submit">Register</Button>
      </div>
    </form>
  </div>;

export default Form = reduxForm({
  form: 'sign_up',
  validate
})(Form);
