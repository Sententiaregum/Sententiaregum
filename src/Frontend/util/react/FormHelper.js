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

import invariant from 'invariant';
import counterpart from 'counterpart';
import FormValueContainer from './FormValueContainer';

/**
 * The state provider is a handler which outsources the state management.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class FormHelper {
  /**
   * Constructor.
   *
   * @param {Object.<String>} formFields           List of all fields in the form.
   * @param {Object.<String>} securedFormFields    List of fields which require protection.
   * @param {Object}          extra                List of extra properties.
   * @param {Function}        stateReceiver        A hook in the associated component to ship state changes.
   * @param {String}          translationNamespace The translation prefix.
   *
   * @returns {void}
   */
  constructor(formFields, securedFormFields, extra, stateReceiver, translationNamespace) {
    this._container  = new FormValueContainer();
    this._formFields = Object.assign({}, formFields, securedFormFields);
    this._secured    = securedFormFields;
    this._extra      = extra;
    this._receiver   = stateReceiver;
    this._namespace  = translationNamespace;
    this._submitted  = false;
  }

  /**
   * Builds the initial state of a form component.
   *
   * @param {Object} errors The form errors (if a component is re-mounted, the errors should be still visible).
   *
   * @returns {Object} The state.
   */
  getInitialState(errors = {}) {
    const hasErrors = 0 < Object.keys(errors).length;
    if (hasErrors) {
      this._submitted = true;
    }

    const copy = this._formFields;
    Object.keys(this._formFields).forEach(index => {
      const persistedValue = this.getValue(null, index);
      if (persistedValue) {
        copy[index] = persistedValue;
      }
    });
    return {
      progress:   false,
      data:       copy,
      success:    false,
      validation: Object.assign({}, {
        errors,
        submitted: hasErrors
      }, this._extra)
    };
  }

  /**
   * Builds a state in case of errors.
   *
   * @param {Object} fields The form fields connected with their current values.
   * @param {Object} errors The list of errors.
   * @param {Object} extra  Extra fields that may change in case of errors.
   *
   * @returns {Object} The new state.
   */
  getErrorState(fields, errors, extra = {}) {
    this._submitted = true;

    invariant(
      Object.keys(fields).length === Object.keys(this._formFields).length,
      'All form fields must be present in order to avoid fields getting lost as React.JS doesn\'t support deep merging!'
    );

    return {
      data:       fields,
      success:    false,
      progress:   false,
      validation: Object.assign({}, {
        errors
      }, extra)
    };
  }

  /**
   * Provider for the success state.
   *
   * @param {Object} fields The current fields.
   *
   * @returns {Object} The new state changeset.
   */
  getSuccessState(fields) {
    this._submitted = true;

    return {
      data:       this._eraseFields(fields),
      success:    true,
      progress:   false,
      validation: Object.assign({}, {
        errors: {}
      }, this._extra)
    };
  }

  /**
   * Provider for the progress bar.
   *
   * @returns {Object} The next state changeset.
   */
  startProgress() {
    return { progress: true };
  }

  /**
   * Gathers a list of translated form fields.
   *
   * @param {String} name The name of the form field.
   *
   * @returns {String} The translated field.
   */
  getTranslatedFormField(name) {
    return counterpart.translate(this.getFormFieldAlias(name));
  }

  /**
   * Gets the form field alias.
   *
   * @param {String} name The field name.
   *
   * @returns {String} The alias.
   */
  getFormFieldAlias(name) {
    return `${this._namespace}.${name}`;
  }

  /**
   * Gets the style names for certain form fields referring to the error list.
   *
   * @param {Array.<String>} errors The form errors.
   *
   * @returns {String} The styles.
   */
  associateFieldsWithStyle(errors) {
    return this._submitted ? (0 === errors.length ? 'success' : 'error') : null;
  }

  /**
   * Factory for a listener which gathers form field changes.
   *
   * @returns {Function} The listener callback.
   */
  getChangeListener() {
    const receiver = this._receiver;
    return e => {
      const target = e.target;

      const change      = {},
          fieldName     = target.getAttribute('name');
      change[fieldName] = target.value;

      if ('undefined' === typeof this._secured[fieldName]) {
        this._container.persistFormValue(this.getFormFieldAlias(fieldName), target.value);
      }
      receiver({ data: change });
    };
  }

  /**
   * Value builder which fetches data from the container if no value is set.
   *
   * @param {*}      state The state.
   * @param {String} name  The field name.
   *
   * @returns {String} The actual field value.
   */
  getValue(state, name) {
    if (!state) {
      const value = this._container.getFormValueForAlias(this.getFormFieldAlias(name));
      return value ? value : '';
    }
    return state;
  }

  /**
   * Removes sensitive data and resets it to the default value.
   *
   * @param  {Object.<String>} fields The form fieldset to change.
   *
   * @returns {Object.<String>} The form fields.
   * @private
   */
  _eraseFields(fields) {
    invariant(
      Object.keys(fields).length === Object.keys(this._formFields).length,
      'All form fields must be present in order to avoid fields getting lost as React.JS doesn\'t support deep merging!'
    );

    Object.keys(this._secured).forEach(fieldName => {
      fields[fieldName] = this._secured[fieldName]; // reset to the default value
    });

    return fields;
  }
}
