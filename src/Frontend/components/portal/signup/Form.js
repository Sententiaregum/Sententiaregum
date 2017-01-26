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
import LoadableButtonBar                                              from '../../form/LoadableButtonBar';
import createRecaptchaWrapper                                         from './createRecaptchaWrapper';
import siteKey                                                        from '../../../config/recaptcha';
import Success                                                        from './Success';
import { Suggestions }                                                from './Suggestions';
import Translate                                                      from 'react-translate-component';
import FormField                                                      from '../../form/FormField';

/**
 * Custom Component builder for selectables
 *
 * @param input
 * @param label
 */
const dropDownComponent = ({ input, label }) =>
  <div >
    <b><Translate content={label} /></b>
    <div onChange={e => input.onChange(e.target.value)}>
      <p><input type="radio" value="de" name="locale" /> Deutsch</p>
      <p><input type="radio" value="en" name="locale" defaultChecked="defaultChecked" /> English (USA)</p>
    </div>
  </div>;

dropDownComponent.propTypes = {
  input: PropTypes.object.isRequired,
  label: PropTypes.string.isRequired
};

const recaptchaComponent = createRecaptchaWrapper(siteKey);

/**
 * Presentational form component
 *
 * @param handleSubmit
 * @param name_suggestions
 * @param success
 * @param submitting
 */
let Form = ({ handleSubmit, name_suggestions, success, submitting }) =>
  <form onSubmit={handleSubmit}>
    <Suggestions suggestions={name_suggestions} />
    {success ? <Success /> : null}
    <Field component={FormField} type="text"     label="pages.portal.create_account.form.username" name="username" autoFocus={true} />
    <Field component={FormField} type="password" label="pages.portal.create_account.form.password" name="password" />
    <Field component={FormField} type="email"    label="pages.portal.create_account.form.email"    name="email" />
    <Field component={dropDownComponent}               label="pages.portal.create_account.form.language" name="locale" />
    <Field component={recaptchaComponent}              label="recaptcha"                                 name="recaptchaHash" success={success} />

    <LoadableButtonBar btnLabel="pages.portal.create_account.form.button" progress={submitting} />
  </form>;

export default Form = reduxForm({
  form: 'sign_up'
})(Form);

Form.propTypes = {
  handleSubmit:     PropTypes.func,
  name_suggestions: PropTypes.array,
  success:          PropTypes.bool.isRequired,
  submitting:       PropTypes.bool
};
