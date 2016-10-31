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


import ProgressBar from 'react-bootstrap/lib/ProgressBar';
import React, { Component } from 'react';
import Translate from 'react-translate-component';
import { runAction, connector } from 'sententiaregum-flux-container';
import userActions from '../../actions/userActions';
import userStore from '../../store/userStore';
import { LOGOUT } from '../../constants/Portal';

/**
 * Logout component.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class Logout extends Component {
  /**
   * Constructor.
   *
   * @param {Object} props The component properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);

    this._redirectAfterLogout = () => this.context.router.replace('/');
  }

  /**
   * Lifecycle hook which triggers the logout process.
   *
   * @returns {void}
   */
  componentDidMount() {
    connector(userStore).subscribe(this._redirectAfterLogout);
    runAction(LOGOUT, userActions, []);
  }

  /**
   * Lifecycle hook which removes the redirect handler.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    connector(userStore).unsubscribe(this._redirectAfterLogout);
  }

  /**
   * Renders a progress to indicate loading during the logout process.
   *
   * @returns {React.Element} The react element.
   */
  render() {
    return (
      <div>
        <h1><Translate content="pages.network.logout" /></h1>
        <ProgressBar active bsStyle="warning" now={100} />
      </div>
    );
  }
}

Logout.contextTypes = {
  router: React.PropTypes.oneOfType([
    React.PropTypes.func,
    React.PropTypes.object
  ])
};
