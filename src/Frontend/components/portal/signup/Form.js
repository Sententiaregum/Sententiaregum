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

import React, { Component }     from 'react';
import LoadableButtonBar        from '../../form/LoadableButtonBar';
import userActions              from '../../../actions/userActions';
import Suggestions              from './Suggestions';
import Success                  from './Success';
import FormHelper               from '../../../util/react/FormHelper';
import FormField                from '../../form/FormField';
import SelectableField          from '../../form/SelectableField';
import deepAssign               from 'deep-assign';
import { connector, runAction } from 'sententiaregum-flux-container';
import localeStore              from '../../../store/localeStore';
import Recaptcha                from 'react-recaptcha';
import siteKey                  from '../../../config/recaptcha';
import update                   from 'react-addons-update';
import userStore                from '../../../store/userStore';
import { CREATE_ACCOUNT }       from '../../../constants/Portal';

/**
 * Form component for the signup page.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * @author Benjamin Bieler <benjaminbieler2014@gmail.com>
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

    this._handleChange = this._handleChange.bind(this);

    const currentState = userStore.getStateValue('creation');
    this.helper        = new FormHelper(
      { username: '', email: '', locale: localeStore.getStateValue('current.locale', 'en'), recaptchaHash: '' },
      { password: '' },
      { suggestions: currentState.name_suggestions },
      nextState => this.setState(deepAssign({ data: this.state.data }, nextState)),
      'pages.portal.create_account.form'
    );

    this.state = this.helper.getInitialState(currentState.errors);
  }

  /**
   * Registers the store.
   *
   * @returns {void}
   */
  componentDidMount() {
    connector(userStore).subscribe(this._handleChange);
  }

  /**
   * Removes the store.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    connector(userStore).unsubscribe(this._handleChange);
  }
  /**
   * Renders the component.
   *
   * @returns {React.Element} The vDOM markup.
   */
  render() {
    const callback = () => {
    };

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
        <Recaptcha
            sitekey={siteKey}
            render='explicit'
            onloadCallback={callback}
            verifyCallback={this.verifyCallback.bind(this)}
        />
        <LoadableButtonBar btnLabel={this.helper.getFormFieldAlias('button')} progress={this.state.progress} />
      </form>
    );
  }

  /**
   * Verifies the callback.
   *
   * @param {string} response Hash the user generates.
   *
   * @returns {void}
   */
  verifyCallback(response) {
    const newState = update(this.state, {
      data: {
        recaptchaHash: { $set: response }
      }
    });
    this.setState(newState);
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

    runAction(CREATE_ACCOUNT, userActions, [{
      username:      this.state.data.username,
      password:      this.state.data.password,
      email:         this.state.data.email,
      locale:        this.state.data.locale,
      recaptchaHash: this.state.data.recaptchaHash
    }]);
  }

  /**
   * Handles store changes.
   *
   * @returns {void}
   * @private
   */
  _handleChange() {
    const state = userStore.getStateValue('creation');
    this.setState(state.success
      ? this.helper.getSuccessState(this.state.data)
      : this.helper.getErrorState(this.state.data, state.errors, { suggestions: state.name_suggestions })
    );
  }
}
