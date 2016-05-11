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

import React from 'react';
import DismissableAlertBox from '../app/markup/DismissableAlertBox';
import Translate from 'react-translate-component';
import PortalActions from '../../actions/PortalActions';
import ActivationStore from '../../store/ActivationStore';

/**
 * Activation component to be used when activating a account.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class ActivateAccount extends React.Component {
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

    this.successHandler = this._success.bind(this);
    this.errorHandler   = this._failure.bind(this);
  }

  /**
   * Lifecycle hook after component mount.
   *
   * @returns {void}
   */
  componentDidMount() {
    ActivationStore.addChangeListener(this.successHandler, 'Activation.Success');
    ActivationStore.addChangeListener(this.errorHandler, 'Activation.Failure');

    PortalActions.activate(this.props.params.name, this.props.params.key);
  }

  /**
   * Lifecycle hook to remove changeset listeners.
   *
   * @returns {void}
   */
  componentWillUnmount() {
    ActivationStore.removeChangeListener(this.successHandler, 'Activation.Success');
    ActivationStore.removeChangeListener(this.errorHandler, 'Activation.Failure');
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
   * Success handler.
   *
   * @returns {void}
   * @private
   */
  _success() {
    this.setState({
      progress: false,
      success:  true
    });
  }

  /**
   * Error handler.
   *
   * @returns {void}
   * @private
   */
  _failure() {
    this.setState({
      progress: false,
      failure:  true
    });
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

ActivateAccount.contextTypes = {
  router: React.PropTypes.oneOfType([
    React.PropTypes.func,
    React.PropTypes.object
  ])
};
