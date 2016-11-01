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
import userActions from '../../../actions/userActions';
import FormHelper from '../../../util/react/FormHelper';
import { runAction, connector } from 'sententiaregum-flux-container';
import deepAssign from 'deep-assign';
import FormField from '../../form/FormField';
import SimpleErrorAlert from '../../app/markup/SimpleErrorAlert';
import userStore from '../../../store/userStore';
import { REQUEST_API_KEY } from '../../../constants/Portal';

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
    this._change = this._change.bind(this);

    const state = userStore.getStateValue('auth.message');
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
    connector(userStore).subscribe(this._change);
  }

  /**
   * Removes the change listener.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    connector(userStore).unsubscribe(this._change);
  }

  /**
   * Renders the dom markup.
   *
   * @returns {React.Element} The markup.
   */
  render() {
    return (
      <form onSubmit={this._login.bind(this)}>
        {!this.state.success && this.helper.isSubmitted()
          ? <SimpleErrorAlert error={this.state.validation.errors} />
          : false
        }
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
    const state = userStore.getStateValue('auth');
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

    runAction(REQUEST_API_KEY, userActions, [{ username: this.state.data.username, password: this.state.data.password }]);
    e.preventDefault();
  }
}

Form.contextTypes = {
  router: React.PropTypes.oneOfType([
    React.PropTypes.func,
    React.PropTypes.object
  ])
};
