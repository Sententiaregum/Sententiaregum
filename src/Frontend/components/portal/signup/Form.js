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
import { registration } from '../../../actions/PortalActions';
import RegistrationStore from '../../../store/RegistrationStore';
import Suggestions from './Suggestions';
import Success from './Success';
import FormHelper from '../../../util/react/FormHelper';
import FormField from '../../form/FormField';
import SelectableField from '../../form/SelectableField';
import deepAssign from 'deep-assign';
import { connector, runAction } from 'sententiaregum-flux-container';
import getStateValue from '../../../store/provider/getStateValue';
import LanguageStore from '../../../store/LanguageStore';

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

    this.handler = this._handleChange.bind(this);

    const currentState = RegistrationStore.getState(), hasState = currentState ? true : false;
    this.helper        = new FormHelper(
      { username: '', email: '', locale: getStateValue(LanguageStore, 'locale', 'en') },
      { password: '' },
      { suggestions: hasState ? currentState.suggestions : [] },
      nextState => this.setState(deepAssign({ data: this.state.data }, nextState)),
      'pages.portal.create_account.form'
    );

    this.state = this.helper.getInitialState(hasState ? currentState.errors : {});
  }

  /**
   * Registers the store.
   *
   * @returns {void}
   */
  componentDidMount() {
    connector(RegistrationStore).useWith(this.handler);
  }

  /**
   * Removes the store.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    connector(RegistrationStore).unsubscribe(this.handler);
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

    runAction(registration, [{
      username: this.state.data.username,
      password: this.state.data.password,
      email:    this.state.data.email,
      locale:   this.state.data.locale
    }]);
  }

  /**
   * Handles store changes.
   *
   * @returns {void}
   * @private
   */
  _handleChange() {
    const state = RegistrationStore.getState();
    if (!state) {
      this.setState(this.helper.getSuccessState(this.state.data));
    } else {
      this.setState(this.helper.getErrorState(this.state.data, state.errors, { suggestions: state.suggestions }));
    }
  }
}
