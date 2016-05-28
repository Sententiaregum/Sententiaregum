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

import React, { Component } from 'react';
import LoadableButtonBar from '../../form/LoadableButtonBar';
import PortalActions from '../../../actions/PortalActions';
import RegistrationStore from '../../../store/RegistrationStore';
import Suggestions from './Suggestions';
import Success from './Success';
import Locale from '../../../util/http/LocaleService';
import FormHelper from '../../../util/react/FormHelper';
import translator from 'counterpart';
import FormField from '../../form/FormField';
import SelectableField from '../../form/SelectableField';
import deepAssign from 'deep-assign';

/**
 * Form component for the signup page.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class Form extends Component {
  /**
   * Constructor.
   *
   * @param {Array} props Component properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);

    this.errorHandler   = this._handleErrors.bind(this);
    this.successHandler = this._renderSuccessBox.bind(this);

    this.helper = new FormHelper(
      { username: '', email: '', locale: Locale.getLocale() },
      { password: '' },
      { suggestions: RegistrationStore.getSuggestions() },
      nextState => this.setState(deepAssign({ data: this.state.data }, nextState)),
      'pages.portal.create_account.form'
    );

    this.state = this.helper.getInitialState(RegistrationStore.getErrors());
  }

  /**
   * Registers the store.
   *
   * @returns {void}
   */
  componentDidMount() {
    RegistrationStore.addChangeListener(this.errorHandler, 'CreateAccount.Error');
    RegistrationStore.addChangeListener(this.successHandler, 'CreateAccount.Success');
  }

  /**
   * Removes the store.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    RegistrationStore.removeChangeListener(this.errorHandler, 'CreateAccount.Error');
    RegistrationStore.removeChangeListener(this.successHandler, 'CreateAccount.Success');
  }

  /**
   * Renders the component.
   *
   * @returns {React.Element} The vDOM markup.
   */
  render() {
    return (
      <form onSubmit={this._createAccount.bind(this)}>
        <Suggestions suggestions={this.state.validation.suggestions} />
        {this.state.success ? <Success /> : null}
        <FormField
          name="username"
          type="text"
          value={this.helper.getValue(this.state.data.username, 'username')}
          autoFocus={true}
          errors={this.state.validation.errors}
          helper={this.helper} />
        <FormField
          name="password"
          type="password"
          value={this.helper.getValue(this.state.data.password, 'password')}
          errors={this.state.validation.errors}
          helper={this.helper} />
        <FormField
          name="email"
          type="email"
          value={this.helper.getValue(this.state.data.email, 'email')}
          errors={this.state.validation.errors}
          helper={this.helper} />
        <SelectableField
          name="locale"
          errors={this.state.validation.errors}
          helper={this.helper}
          value={this.helper.getValue(this.state.data.locale, 'locale')}
          options={{ de: 'Deutsch (Deutschland)', en: 'English (USA)' }} />
        <LoadableButtonBar btnLabel={this.helper.getFormFieldAlias('button')} progress={this.state.progress} />
      </form>
    );
  }

  /**
   * Hook to create the new account.
   *
   * @param {Object} e Event object.
   *
   * @returns {void}
   * @private
   */
  _createAccount(e) {
    e.preventDefault();
    this.setState(this.helper.startProgress());

    PortalActions.registration({
      username: this.state.data.username,
      password: this.state.data.password,
      email:    this.state.data.email,
      locale:   this.state.data.locale
    });
  }

  /**
   * Handles the errors from the registration store.
   *
   * @returns {void}
   * @private
   */
  _handleErrors() {
    this.setState(this.helper.getErrorState(this.state.data, RegistrationStore.getErrors(), { suggestions: RegistrationStore.getSuggestions() }));
  }

  /**
   * Handles a registration success.
   *
   * @returns {void}
   * @private
   */
  _renderSuccessBox() {
    this.setState(this.helper.getSuccessState(this.state.data));
  }
}
