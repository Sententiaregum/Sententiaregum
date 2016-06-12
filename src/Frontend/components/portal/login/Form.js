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
import { authenticate } from '../../../actions/PortalActions';
import AuthenticationStore from '../../../store/AuthenticationStore';
import FormHelper from '../../../util/react/FormHelper';
import { runAction, connector } from 'sententiaregum-flux-container';
import deepAssign from 'deep-assign';
import FormField from '../../form/FormField';
import SimpleErrorAlert from '../../app/markup/SimpleErrorAlert';

/**
 * Form component for the login form.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class Form extends Component {
  /**
   * Constructor.
   *
   * @param {Object} props Component properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);
    this.handler = this._change.bind(this);

    const state = AuthenticationStore.getState().message;
    this.helper = new FormHelper(
      { username: '' },
      { password: '' },
      {},
      nextState => this.setState(deepAssign({ data: this.state.data }, nextState)),
      'pages.portal.login.form',
      false
    );

    this.state = this.helper.getInitialState(state ? state : {});
  }

  /**
   * Mounts the change listeners on the store.
   *
   * @returns {void}
   */
  componentDidMount() {
    connector(AuthenticationStore).useWith(this.handler);
  }

  /**
   * Removes the change listener.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    connector(AuthenticationStore).unsubscribe(this.handler);
  }

  /**
   * Renders the dom markup.
   *
   * @returns {React.Element} The markup.
   */
  render() {
    let errorBox;
    if (!this.state.success && this.helper.isSubmitted()) {
      errorBox = <SimpleErrorAlert error={this.state.validation.errors} />;
    }

    return (
      <form onSubmit={this._login.bind(this)}>
        {errorBox}
        <FormField
          name="username"
          type="text"
          value={this.helper.getValue(this.state.data.username, 'username')}
          autoFocus={true}
          errors={{}}
          helper={this.helper} />
        <FormField
          name="password"
          type="password"
          value={this.helper.getValue(this.state.data.password, 'password')}
          errors={{}}
          helper={this.helper} />

        <LoadableButtonBar progress={this.state.progress} btnLabel={this.helper.getFormFieldAlias('button')} />
      </form>
    );
  }

  /**
   * Change handler.
   *
   * @returns {void}
   * @private
   */
  _change() {
    const state = AuthenticationStore.getState();
    if (state.message) {
      this.setState(this.helper.getErrorState(this.state.data, state.message));

      return;
    }

    this.helper.purge();

    // redirect to the internal page
    this.context.router.replace('/dashboard');
  }

  /**
   * Login handler.
   *
   * @param {Object} e Event object.
   *
   * @returns {void}
   * @private
   */
  _login(e) {
    this.setState(this.helper.startProgress());

    runAction(authenticate, [this.state.data.username, this.state.data.password]);
    e.preventDefault();
  }
}

Form.contextTypes = {
  router: React.PropTypes.oneOfType([
    React.PropTypes.func,
    React.PropTypes.object
  ])
};
