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
import DismissableAlertBox      from '../app/markup/DismissableAlertBox';
import Translate                from 'react-translate-component';
import userActions              from '../../actions/userActions';
import userStore                from '../../store/userStore';
import { ACTIVATE_ACCOUNT }     from '../../constants/Portal';
import { connector, runAction } from 'sententiaregum-flux-container';

/**
 * Activation component to be used when activating a account.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class ActivateAccount extends Component {
  /**
   * Constructor.
   *
   * @param {Array} props Properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);
    this.state = {
      progress: true,
      success:  false,
      failure:  false
    };

    this._handleChange = this._handleChange.bind(this);
  }

  /**
   * Lifecycle hook after component mount.
   *
   * @returns {void}
   */
  componentDidMount() {
    connector(userStore).subscribe(this._handleChange);
    runAction(ACTIVATE_ACCOUNT, userActions, [{ username: this.props.params.name, key: this.props.params.key }]);
  }

  /**
   * Lifecycle hook to remove changeset listeners.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    connector(userStore).unsubscribe(this._handleChange);
  }

  /**
   * Render.
   *
   * @returns {React.Element} The markup.
   */
  render() {
    const content = this._getState();

    if (this.state.success) {
      this.context.router.replace('/');
    }
    return (
      <div>
        <h1><Translate content="pages.portal.activate.headline" /></h1>
        <DismissableAlertBox bsStyle={this._getBoxStyle()}>
          {content}
        </DismissableAlertBox>
      </div>
    );
  }

  /**
   * Renders the bootstrap style.
   *
   * @returns {string} The bootstrap style alias.
   * @private
   */
  _getBoxStyle() {
    if (this.state.progress) {
      return 'info';
    }

    return this.state.success ? 'success' : 'danger';
  }

  /**
   * Change handler.
   *
   * @returns {void}
   * @private
   */
  _handleChange() {
    const state = userStore.getStateValue('activation');
    if (state.success) {
      this.setState({
        progress: false,
        success:  true
      });
    } else {
      this.setState({
        progress: false,
        failure:  true
      });
    }
  }

  /**
   * Provider which renders the appropriate translation result.
   *
   * @returns {React.Element} The markup for the state.
   * @private
   */
  _getState() {
    if (this.state.progress) {
      return <span><Translate content="pages.portal.activate.progress" /> {this.props.params.name}...</span>;
    }
    return <Translate content={this.state.success ? 'pages.portal.activate.success' : 'pages.portal.activate.error'} />;
  }
}

ActivateAccount.propTypes = {
  params: React.PropTypes.object
};

ActivateAccount.contextTypes = {
  router: React.PropTypes.oneOfType([
    React.PropTypes.func,
    React.PropTypes.object
  ])
};
